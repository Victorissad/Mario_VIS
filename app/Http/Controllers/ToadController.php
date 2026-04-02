<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ToadController extends Controller
{
    private string $apiUrl;
    private string $jwtToken;

    public function __construct()
    {
        $this->apiUrl = config('services.toad.url');
        $this->jwtToken = config('services.toad.token');
    }

    private function token(): string
    {
        $userData = session('toad_user');
        return $userData['token'] ?? $this->jwtToken;
    }

    private function apiGet(string $endpoint)
    {
        return Http::withToken($this->token())->get($this->apiUrl . $endpoint);
    }

    private function apiPost(string $endpoint, array $data)
    {
        return Http::withToken($this->token())->post($this->apiUrl . $endpoint, $data);
    }

    private function apiPut(string $endpoint, array $data)
    {
        return Http::withToken($this->token())->put($this->apiUrl . $endpoint, $data);
    }

    private function apiDelete(string $endpoint)
    {
        return Http::withToken($this->token())->delete($this->apiUrl . $endpoint);
    }

    // ─── FILMS ───────────────────────────────────────────────────────────────

    public function getFilms()
    {
        return view('films', ['allowedLimits' => [10, 20, 50]]);
    }

    public function getFilmsData(Request $request)
    {
        $allowedLimits = [10, 20, 50];
        $limit = (int) $request->input('limit', 10);
        if (!in_array($limit, $allowedLimits)) {
            $limit = 10;
        }

        $page   = max(1, (int) $request->input('page', 1));
        $offset = ($page - 1) * $limit;

        $countResponse = $this->apiGet('/films/count');
        $totalFilms    = $countResponse->successful() ? (int) $countResponse->body() : 0;
        $totalPages    = $totalFilms > 0 ? (int) ceil($totalFilms / $limit) : 1;

        $response = $this->apiGet('/films?limit=' . $limit . '&offset=' . $offset);

        if ($response->successful()) {
            $films = array_slice($response->json(), 0, $limit);
            return response()->json([
                'films'       => $films,
                'currentPage' => $page,
                'limit'       => $limit,
                'totalFilms'  => $totalFilms,
                'totalPages'  => $totalPages,
            ]);
        }

        return response()->json(['films' => [], 'currentPage' => 1, 'limit' => $limit, 'totalFilms' => 0, 'totalPages' => 1]);
    }

    public function showCreateFilm()
    {
        return view('film-form', ['film' => null]);
    }

    public function createFilm(Request $request)
    {
        $request->validate([
            'title'          => 'required|string|max:255',
            'rentalDuration' => 'required|integer|min:1',
        ]);

        $data = [
            'title'              => $request->title,
            'description'        => $request->description,
            'releaseYear'        => $request->releaseYear ? (int) $request->releaseYear : null,
            'rentalDuration'     => (int) $request->rentalDuration,
            'rentalRate'         => $request->rentalRate ? (float) $request->rentalRate : 4.99,
            'length'             => $request->length ? (int) $request->length : null,
            'replacementCost'    => $request->replacementCost ? (float) $request->replacementCost : 19.99,
            'rating'             => $request->rating,
            'specialFeatures'    => $request->specialFeatures ? implode(',', $request->specialFeatures) : null,
            'originalLanguageId' => $request->originalLanguageId ? (int) $request->originalLanguageId : null,
        ];

        $response = $this->apiPost('/films', $data);

        if ($response->successful()) {
            $film = $response->json();
            $filmId = $film['filmId'] ?? null;
            if ($filmId) {
                return redirect('/films/' . $filmId)->with('success', 'Film ajouté avec succès.');
            }
            return redirect('/films')->with('success', 'Film ajouté avec succès.');
        }

        return back()->withInput()->withErrors(['api' => 'Erreur lors de l\'ajout du film. (' . $response->status() . ')']);
    }

    public function showEditFilm($id)
    {
        $response = $this->apiGet('/films/' . $id);

        if ($response->successful()) {
            return view('film-form', ['film' => $response->json()]);
        }

        return redirect('/films')->withErrors(['api' => 'Film introuvable.']);
    }

    public function updateFilm(Request $request, $id)
    {
        $request->validate([
            'title'          => 'required|string|max:255',
            'rentalDuration' => 'required|integer|min:1',
        ]);

        $data = [
            'title'              => $request->title,
            'description'        => $request->description,
            'releaseYear'        => $request->releaseYear ? (int) $request->releaseYear : null,
            'rentalDuration'     => (int) $request->rentalDuration,
            'rentalRate'         => $request->rentalRate ? (float) $request->rentalRate : 4.99,
            'length'             => $request->length ? (int) $request->length : null,
            'replacementCost'    => $request->replacementCost ? (float) $request->replacementCost : 19.99,
            'rating'            => $request->rating,
            'specialFeatures'   => $request->specialFeatures ? implode(',', $request->specialFeatures) : null,
            'originalLanguageId'=> $request->originalLanguageId ? (int) $request->originalLanguageId : null,
        ];

        $response = $this->apiPut('/films/' . $id, $data);

        if ($response->successful()) {
            return redirect('/films/' . $id)->with('success', 'Film modifié avec succès.');
        }

        return back()->withInput()->withErrors(['api' => 'Erreur lors de la modification. (' . $response->status() . ')']);
    }

    public function deleteFilm($id)
    {
        $this->apiDelete('/films/' . $id);
        return redirect('/films')->with('success', 'Film supprimé.');
    }

    public function getFilmDetail($id)
    {
        $response = $this->apiGet('/films/' . $id);

        if ($response->successful()) {
            return view('film-detail', ['film' => $response->json()]);
        }

        return response()->json([
            'error'  => 'Film non trouvé',
            'status' => $response->status(),
            'body'   => $response->body(),
        ], 404);
    }

    // ─── INVENTAIRES / STOCK ─────────────────────────────────────────────────

    public function getInventories()
    {
        $response = $this->apiGet('/inventories');

        if (!$response->successful()) {
            return response()->json([
                'error'  => 'Impossible de récupérer les inventaires',
                'status' => $response->status(),
                'body'   => $response->body(),
            ], 500);
        }

        $inventories = $response->json();

        // Récupérer tous les films pour avoir les titres
        $filmsResponse = $this->apiGet('/films');
        $filmMap = [];
        if ($filmsResponse->successful()) {
            foreach ($filmsResponse->json() as $film) {
                $filmMap[$film['filmId']] = $film;
            }
        }

        // Grouper par film + magasin et compter
        $grouped = [];
        foreach ($inventories as $inventory) {
            $filmId  = $inventory['filmId'];
            $storeId = $inventory['storeId'];
            $key     = $filmId . '_' . $storeId;

            if (!isset($grouped[$key])) {
                $film = $filmMap[$filmId] ?? null;
                $grouped[$key] = [
                    'filmId'   => $filmId,
                    'storeId'  => $storeId,
                    'title'    => $film['title'] ?? 'Film #' . $filmId,
                    'rating'   => $film['rating'] ?? null,
                    'count'    => 0,
                    'copies'   => [], // liste des inventoryId individuels
                ];
            }
            $grouped[$key]['count']++;
            $grouped[$key]['copies'][] = $inventory['inventoryId'];
        }

        usort($grouped, fn($a, $b) => strcmp($a['title'], $b['title']));

        return view('inventories', [
            'grouped'    => array_values($grouped),
            'totalItems' => count($inventories),
        ]);
    }

    public function getInventoryDetail($id)
    {
        $inventoryResponse = $this->apiGet('/inventories/' . $id);

        if ($inventoryResponse->successful()) {
            $inventory = $inventoryResponse->json();

            $film = null;
            if (isset($inventory['filmId'])) {
                $filmResponse = $this->apiGet('/films/' . $inventory['filmId']);
                if ($filmResponse->successful()) {
                    $film = $filmResponse->json();
                }
            }

            $store = null;
            if (isset($inventory['storeId'])) {
                $storeResponse = $this->apiGet('/stores/' . $inventory['storeId']);
                if ($storeResponse->successful()) {
                    $store = $storeResponse->json();
                }
            }

            return view('inventory-detail', [
                'inventory' => $inventory,
                'film'      => $film,
                'store'     => $store,
            ]);
        }

        return response()->json([
            'error'  => 'Inventaire non trouvé',
            'status' => $inventoryResponse->status(),
            'body'   => $inventoryResponse->body(),
        ], 404);
    }

    public function createInventory(Request $request)
    {
        $request->validate([
            'filmId'  => 'required|integer|min:1',
            'storeId' => 'required|integer|min:1',
        ]);

        $response = $this->apiPost('/inventories', [
            'filmId'  => (int) $request->filmId,
            'storeId' => (int) $request->storeId,
        ]);

        if ($response->successful()) {
            return back()->with('success', 'Exemplaire ajouté au stock.');
        }

        return back()->withErrors(['api' => 'Erreur lors de l\'ajout. (' . $response->status() . ')']);
    }

    public function updateInventory(Request $request, $id)
    {
        $request->validate(['storeId' => 'required|integer|min:1']);

        // Récupérer le filmId existant pour le conserver
        $current = $this->apiGet('/inventories/' . $id);
        if (!$current->successful()) {
            return back()->withErrors(['api' => 'Exemplaire introuvable.']);
        }

        $filmId = $current->json()['filmId'];

        $response = $this->apiPut('/inventories/' . $id, [
            'filmId'  => $filmId,
            'storeId' => (int) $request->storeId,
        ]);

        if ($response->successful()) {
            return redirect('/inventories')->with('success', 'Exemplaire #' . $id . ' déplacé vers le magasin #' . $request->storeId . '.');
        }

        return back()->withErrors(['api' => 'Erreur lors de la modification. (' . $response->status() . ')']);
    }

    public function deleteInventory($id)
    {
        $this->apiDelete('/inventories/' . $id);
        return redirect('/inventories')->with('success', 'Exemplaire supprimé du stock.');
    }

}
