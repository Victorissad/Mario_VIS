<?php

namespace App\Http\Controllers;

use App\Services\ToadStoreService;
use App\Services\ToadInventoryService;

/**
 * Contrôleur pour gérer les magasins
 *
 * Ce contrôleur gère l'affichage des magasins et de leur inventaire.
 */
class StoreController extends Controller
{
    private ToadStoreService $storeService;
    private ToadInventoryService $inventoryService;

    public function __construct(
        ToadStoreService $storeService,
        ToadInventoryService $inventoryService
    ) {
        // Middleware auth désactivé pour tester
        // $this->middleware('auth');

        $this->storeService = $storeService;
        $this->inventoryService = $inventoryService;
    }

    /**
     * Affiche la liste de tous les magasins
     *
     * Route: GET /stores
     * Vue: stores/index.blade.php
     *
     * Explication: Affiche tous les magasins sous forme de cartes avec:
     * - Le nom du responsable
     * - Le nombre total d'exemplaires du magasin
     * - Un bouton pour voir le détail
     */
    public function index()
    {
        // Récupérer tous les magasins
        $stores = $this->storeService->getAllStores();

        // Pour chaque magasin, compter le nombre d'exemplaires
        if ($stores) {
            // Récupérer tous les exemplaires une seule fois
            $allInventories = $this->inventoryService->getAllInventories();

            // Pour chaque magasin, filtrer ses exemplaires
            foreach ($stores as &$store) {
                $storeInventories = array_filter($allInventories ?? [], function($inv) use ($store) {
                    return $inv['storeId'] === $store['storeId'];
                });

                // Ajouter le compteur au tableau du magasin
                $store['inventoryCount'] = count($storeInventories);
            }
        }

        return view('stores.index', [
            'stores' => $stores ?? []
        ]);
    }

    /**
     * Affiche le détail d'un magasin avec son inventaire complet
     *
     * Route: GET /stores/{id}
     * Vue: stores/show.blade.php
     *
     * @param int $id L'ID du magasin
     *
     * Explication: Affiche:
     * - Les infos du magasin (responsable, adresse)
     * - Les statistiques (total, disponibles, loués)
     * - La liste complète des DVD du magasin dans un tableau
     */
    public function show($id)
    {
        // Récupérer les infos du magasin
        $store = $this->storeService->getStoreById($id);

        if (!$store) {
            abort(404, 'Magasin non trouvé');
        }

        // Récupérer tous les exemplaires et filtrer ceux du magasin
        $allInventories = $this->inventoryService->getAllInventories();
        $storeInventories = array_filter($allInventories ?? [], function($inv) use ($id) {
            return $inv['storeId'] === (int)$id;
        });

        // Récupérer les statistiques depuis le nouveau endpoint
        $statistics = $this->storeService->getStoreStatistics($id);

        return view('stores.show', [
            'store' => $store,
            'inventories' => array_values($storeInventories), // array_values pour réindexer
            'statistics' => $statistics
        ]);
    }
}
