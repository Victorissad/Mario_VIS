<?php

namespace App\Http\Controllers;

use App\Services\ToadInventoryService;
use App\Services\ToadStoreService;
use App\Services\ToadFilmService;
use Illuminate\Http\Request;

/**
 * Contrôleur pour gérer l'inventaire des DVD
 *
 * Ce contrôleur gère toutes les pages et actions liées aux exemplaires
 * de films (inventory). Il utilise les services pour communiquer avec l'API.
 */
class InventoryController extends Controller
{
    private ToadInventoryService $inventoryService;
    private ToadStoreService $storeService;
    private ToadFilmService $filmService;

    public function __construct(
        ToadInventoryService $inventoryService,
        ToadStoreService $storeService,
        ToadFilmService $filmService
    ) {
        // Middleware auth désactivé pour tester
        // $this->middleware('auth');

        $this->inventoryService = $inventoryService;
        $this->storeService = $storeService;
        $this->filmService = $filmService;
    }

    /**
     * Affiche la liste de tous les exemplaires (page principale)
     *
     * Route: GET /inventories
     * Vue: inventories/index.blade.php
     *
     * Explication: Récupère tous les DVD de tous les magasins et
     * les affiche dans un tableau avec leurs informations.
     */
    public function index()
    {
        $inventories = $this->inventoryService->getAllInventories() ?? [];

        // Grouper par film + magasin et compter les exemplaires
        $grouped = [];
        foreach ($inventories as $inventory) {
            $filmId  = $inventory['filmId'];
            $storeId = $inventory['storeId'];
            $key     = $filmId . '_' . $storeId;

            if (!isset($grouped[$key])) {
                $grouped[$key] = [
                    'filmId'  => $filmId,
                    'storeId' => $storeId,
                    'title'   => $inventory['film']['title'] ?? 'Film #' . $filmId,
                    'rating'  => $inventory['film']['rating'] ?? null,
                    'count'   => 0,
                ];
            }
            $grouped[$key]['count']++;
        }

        // Trier par titre
        usort($grouped, fn($a, $b) => strcmp($a['title'], $b['title']));

        return view('inventories.index', [
            'grouped'    => $grouped,
            'totalItems' => count($inventories),
        ]);
    }

    /**
     * Affiche le détail d'un exemplaire spécifique
     *
     * Route: GET /inventories/{id}
     * Vue: inventories/show.blade.php
     *
     * @param int $id L'ID de l'exemplaire
     *
     * Explication: Affiche toutes les infos d'un DVD: film, magasin,
     * date de MAJ, avec boutons pour modifier ou supprimer.
     */
    public function show($id)
    {
        // Récupérer l'exemplaire depuis l'API
        $inventory = $this->inventoryService->getInventoryById($id);

        // Si l'exemplaire n'existe pas, afficher une erreur 404
        if (!$inventory) {
            abort(404, 'Exemplaire non trouvé');
        }

        return view('inventories.show', [
            'inventory' => $inventory
        ]);
    }

    /**
     * Affiche le formulaire pour ajouter des exemplaires
     *
     * Route: GET /inventories/create
     * Vue: inventories/create.blade.php
     *
     * Explication: Affiche un formulaire avec:
     * - Une liste déroulante de films
     * - Une liste déroulante de magasins
     * - Un champ nombre d'exemplaires
     */
    public function create()
    {
        // Récupérer les films et magasins pour les listes déroulantes
        $films = $this->filmService->getAllFilms();
        $stores = $this->storeService->getAllStores();

        return view('inventories.create', [
            'films' => $films ?? [],
            'stores' => $stores ?? []
        ]);
    }

    /**
     * Traite la soumission du formulaire d'ajout
     *
     * Route: POST /inventories
     * Redirige vers: Liste des exemplaires
     *
     * @param Request $request Les données du formulaire
     *
     * Explication: Valide les données, puis crée N exemplaires identiques
     * en appelant l'API N fois.
     */
    public function store(Request $request)
    {
        // Validation des données du formulaire
        $validated = $request->validate([
            'film_id' => 'required|integer|min:1',
            'store_id' => 'required|integer|min:1',
            'count' => 'required|integer|min:1|max:100'
        ], [
            'film_id.required' => 'Le film est obligatoire',
            'store_id.required' => 'Le magasin est obligatoire',
            'count.required' => 'Le nombre d\'exemplaires est obligatoire',
            'count.min' => 'Le nombre d\'exemplaires doit être au moins 1',
            'count.max' => 'Le nombre d\'exemplaires ne peut pas dépasser 100'
        ]);

        // Créer les exemplaires via l'API
        $created = $this->inventoryService->createMultipleInventories(
            $validated['film_id'],
            $validated['store_id'],
            $validated['count']
        );

        // Vérifier combien ont été créés
        if (count($created) === $validated['count']) {
            // Succès total
            return redirect()
                ->route('inventories.index')
                ->with('success', count($created) . ' exemplaire(s) créé(s) avec succès.');
        } else if (count($created) > 0) {
            // Succès partiel
            return redirect()
                ->route('inventories.index')
                ->with('success', 'Seulement ' . count($created) . ' exemplaire(s) sur ' . $validated['count'] . ' ont été créés.');
        }

        // Échec total
        return back()
            ->withInput()
            ->with('error', 'Une erreur est survenue lors de la création des exemplaires.');
    }

    /**
     * Affiche le formulaire de modification d'un exemplaire
     *
     * Route: GET /inventories/{id}/edit
     * Vue: inventories/edit.blade.php
     *
     * @param int $id L'ID de l'exemplaire
     *
     * Explication: Permet de changer le magasin d'un DVD.
     * Le film reste en lecture seule (non modifiable).
     */
    public function edit($id)
    {
        // Récupérer l'exemplaire à modifier
        $inventory = $this->inventoryService->getInventoryById($id);

        if (!$inventory) {
            abort(404, 'Exemplaire non trouvé');
        }

        // Récupérer la liste des magasins pour le formulaire
        $stores = $this->storeService->getAllStores();

        return view('inventories.edit', [
            'inventory' => $inventory,
            'stores' => $stores ?? []
        ]);
    }

    /**
     * Traite la soumission du formulaire de modification
     *
     * Route: PUT /inventories/{id}
     * Redirige vers: Page de détail de l'exemplaire
     *
     * @param Request $request Les données du formulaire
     * @param int $id L'ID de l'exemplaire
     *
     * Explication: Met à jour uniquement le magasin de l'exemplaire.
     * Le film reste inchangé.
     */
    public function update(Request $request, $id)
    {
        // Validation
        $validated = $request->validate([
            'store_id' => 'required|integer|min:1'
        ], [
            'store_id.required' => 'Le magasin est obligatoire'
        ]);

        // Récupérer l'exemplaire actuel pour obtenir le filmId
        $currentInventory = $this->inventoryService->getInventoryById($id);

        if (!$currentInventory) {
            abort(404, 'Exemplaire non trouvé');
        }

        // Mise à jour: le film reste le même, seul le magasin change
        $inventory = $this->inventoryService->updateInventory(
            $id,
            $currentInventory['filmId'],  // Film inchangé
            $validated['store_id']        // Nouveau magasin
        );

        if ($inventory) {
            return redirect()
                ->route('inventories.show', $id)
                ->with('success', 'L\'exemplaire a été mis à jour avec succès.');
        }

        return back()
            ->withInput()
            ->with('error', 'Une erreur est survenue lors de la mise à jour de l\'exemplaire.');
    }

    /**
     * Supprime un exemplaire
     *
     * Route: DELETE /inventories/{id}
     * Redirige vers: Liste des exemplaires
     *
     * @param int $id L'ID de l'exemplaire
     *
     * Explication: Tente de supprimer le DVD. Si il est loué,
     * la suppression échoue avec un message d'erreur explicite.
     */
    public function destroy($id)
    {
        try {
            // Tenter la suppression
            $success = $this->inventoryService->deleteInventory($id);

            if ($success) {
                return redirect()
                    ->route('inventories.index')
                    ->with('success', 'L\'exemplaire a été supprimé avec succès.');
            }

            return back()
                ->with('error', 'Une erreur est survenue lors de la suppression de l\'exemplaire.');

        } catch (\Exception $e) {
            // Si l'exception vient du service (DVD loué)
            return back()
                ->with('error', $e->getMessage());
        }
    }
}
