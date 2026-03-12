<?php

namespace App\Http\Controllers;

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

    private function apiGet(string $endpoint)
    {
        return Http::withToken($this->jwtToken)->get($this->apiUrl . $endpoint);
    }

    public function getFilms()
    {
        $response = $this->apiGet('/films');

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
        $response = $this->apiGet('/inventories');

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
        $response = $this->apiGet('/stores');

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
        $response = $this->apiGet('/films/' . $id);

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
        $storeResponse = $this->apiGet('/stores/' . $id);

        if ($storeResponse->successful()) {
            $store = $storeResponse->json();

            $inventoriesResponse = $this->apiGet('/inventories');
            $inventories = [];
            if ($inventoriesResponse->successful()) {
                $allInventories = $inventoriesResponse->json();
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
