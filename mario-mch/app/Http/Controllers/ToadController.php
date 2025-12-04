<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class ToadController extends Controller
{
    private string $apiUrl;

    public function __construct()
    {
        $this->apiUrl = config('services.toad.url');
    }

    public function getFilms()
    {
        // On fait un GET simple sans token
        $response = Http::get($this->apiUrl . '/films');

        if ($response->successful()) {
            $films = $response->json();
            return view('films', ['films' => $films]);
        } else {
            return response()->json([
                'error' => 'Impossible de récupérer les films',
                'status' => $response->status(),
                'body' => $response->body()
            ], 500);
        }
    }

    public function getInventories()
    {
        // Récupère tous les inventaires depuis l'API
        $response = Http::get($this->apiUrl . '/inventories');

        if ($response->successful()) {
            $inventories = $response->json();
            return view('inventories', ['inventories' => $inventories]);
        } else {
            return response()->json([
                'error' => 'Impossible de récupérer les inventaires',
                'status' => $response->status(),
                'body' => $response->body()
            ], 500);
        }
    }

    public function getStores()
    {
        // Récupère tous les magasins depuis l'API
        $response = Http::get($this->apiUrl . '/stores');

        if ($response->successful()) {
            $stores = $response->json();
            return view('stores', ['stores' => $stores]);
        } else {
            return response()->json([
                'error' => 'Impossible de récupérer les magasins',
                'status' => $response->status(),
                'body' => $response->body()
            ], 500);
        }
    }

    public function getFilmDetail($id)
    {
        // Récupère le détail d'un film depuis l'API
        $response = Http::get($this->apiUrl . '/films/' . $id);

        if ($response->successful()) {
            $film = $response->json();
            return view('film-detail', ['film' => $film]);
        } else {
            return response()->json([
                'error' => 'Film non trouvé',
                'status' => $response->status(),
                'body' => $response->body()
            ], 404);
        }
    }

    public function getInventoryDetail($id)
    {
        // Récupère le détail d'un inventaire depuis l'API
        $inventoryResponse = Http::get($this->apiUrl . '/inventories/' . $id);

        if ($inventoryResponse->successful()) {
            $inventory = $inventoryResponse->json();

            // Récupère aussi les infos du film associé
            $film = null;
            if (isset($inventory['filmId'])) {
                $filmResponse = Http::get($this->apiUrl . '/films/' . $inventory['filmId']);
                if ($filmResponse->successful()) {
                    $film = $filmResponse->json();
                }
            }

            // Récupère aussi les infos du magasin associé
            $store = null;
            if (isset($inventory['storeId'])) {
                $storeResponse = Http::get($this->apiUrl . '/stores/' . $inventory['storeId']);
                if ($storeResponse->successful()) {
                    $store = $storeResponse->json();
                }
            }

            return view('inventory-detail', [
                'inventory' => $inventory,
                'film' => $film,
                'store' => $store
            ]);
        } else {
            return response()->json([
                'error' => 'Inventaire non trouvé',
                'status' => $inventoryResponse->status(),
                'body' => $inventoryResponse->body()
            ], 404);
        }
    }

    public function getStoreDetail($id)
    {
        // Récupère le détail d'un magasin depuis l'API
        $storeResponse = Http::get($this->apiUrl . '/stores/' . $id);

        if ($storeResponse->successful()) {
            $store = $storeResponse->json();

            // Récupère les inventaires de ce magasin
            $inventoriesResponse = Http::get($this->apiUrl . '/inventories');
            $inventories = [];
            if ($inventoriesResponse->successful()) {
                $allInventories = $inventoriesResponse->json();
                // Filtre pour garder seulement les inventaires de ce magasin
                $inventories = array_filter($allInventories, function($inv) use ($id) {
                    return isset($inv['storeId']) && $inv['storeId'] == $id;
                });
            }

            return view('store-detail', [
                'store' => $store,
                'inventories' => $inventories
            ]);
        } else {
            return response()->json([
                'error' => 'Magasin non trouvé',
                'status' => $storeResponse->status(),
                'body' => $storeResponse->body()
            ], 404);
        }
    }
}
