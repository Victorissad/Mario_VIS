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

    private function apiGet(string $endpoint)
    {
        return Http::withToken($this->jwtToken)->get($this->apiUrl . $endpoint);
    }

    private function apiPost(string $endpoint, array $data)
    {
        return Http::withToken($this->jwtToken)->post($this->apiUrl . $endpoint, $data);
    }

    private function apiPut(string $endpoint, array $data)
    {
        return Http::withToken($this->jwtToken)->put($this->apiUrl . $endpoint, $data);
    }

    private function apiDelete(string $endpoint)
    {
        return Http::withToken($this->jwtToken)->delete($this->apiUrl . $endpoint);
    }

    // ─── FILMS ───────────────────────────────────────────────────────────────

    public function getFilms()
    {
        $response = $this->apiGet('/films');

        if ($response->successful()) {
            return view('films', ['films' => $response->json()]);
        }

        return response()->json([
            'error'  => 'Impossible de récupérer les films',
            'status' => $response->status(),
            'body'   => $response->body(),
        ], 500);
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
            'length'             => $request->length ? (int) $request->length : null,
            'rating'             => $request->rating,
            'specialFeatures'    => $request->specialFeatures,
            'originalLanguageId' => $request->originalLanguageId ? (int) $request->originalLanguageId : null,
        ];

        $response = $this->apiPost('/films', $data);

        if ($response->successful()) {
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
            'length'             => $request->length ? (int) $request->length : null,
            'rating'            => $request->rating,
            'specialFeatures'   => $request->specialFeatures,
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

        if ($response->successful()) {
            return view('inventories', ['inventories' => $response->json()]);
        }

        return response()->json([
            'error'  => 'Impossible de récupérer les inventaires',
            'status' => $response->status(),
            'body'   => $response->body(),
        ], 500);
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

    public function deleteInventory($id)
    {
        $this->apiDelete('/inventories/' . $id);
        return redirect('/inventories')->with('success', 'Exemplaire supprimé du stock.');
    }

    // ─── MAGASINS ────────────────────────────────────────────────────────────

    public function getStores()
    {
        $response = $this->apiGet('/stores');

        if ($response->successful()) {
            $stores = $response->json();
            return view('stores', ['stores' => $stores]);
        } else {
            return response()->json([
                'error'  => 'Impossible de récupérer les magasins',
                'status' => $response->status(),
                'body'   => $response->body(),
            ], 500);
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
                $inventories = array_filter($allInventories, function ($inv) use ($id) {
                    return isset($inv['storeId']) && $inv['storeId'] == $id;
                });
            }

            return view('store-detail', [
                'store'       => $store,
                'inventories' => $inventories,
            ]);
        }

        return response()->json([
            'error'  => 'Magasin non trouvé',
            'status' => $storeResponse->status(),
            'body'   => $storeResponse->body(),
        ], 404);
    }

    // ─── CLIENTS ─────────────────────────────────────────────────────────────

    public function getCustomers()
    {
        $response = $this->apiGet('/customers');

        if ($response->successful()) {
            return view('customers', ['customers' => $response->json()]);
        }

        return response()->json([
            'error'  => 'Impossible de récupérer les clients',
            'status' => $response->status(),
            'body'   => $response->body(),
        ], 500);
    }

    public function showEditCustomer($id)
    {
        $response = $this->apiGet('/customers/' . $id);

        if ($response->successful()) {
            return view('customer-edit', ['customer' => $response->json()]);
        }

        return redirect('/customers')->withErrors(['api' => 'Client introuvable.']);
    }

    public function updateCustomer(Request $request, $id)
    {
        $request->validate([
            'firstName' => 'required|string|max:100',
            'lastName'  => 'required|string|max=100',
            'email'     => 'required|email',
        ]);

        $currentResponse = $this->apiGet('/customers/' . $id);
        if (!$currentResponse->successful()) {
            return back()->withErrors(['api' => 'Client introuvable.']);
        }
        $current = $currentResponse->json();

        $data = [
            'storeId'   => $current['storeId'],
            'firstName' => $request->firstName,
            'lastName'  => $request->lastName,
            'email'     => $request->email,
            'password'  => $current['password'],
            'addressId' => $current['addressId'],
            'active'    => $request->has('active') ? true : false,
        ];

        $response = $this->apiPut('/customers/' . $id, $data);

        if ($response->successful()) {
            return redirect('/customers')->with('success', 'Client modifié avec succès.');
        }

        return back()->withInput()->withErrors(['api' => 'Erreur lors de la modification. (' . $response->status() . ')']);
    }

    public function deleteCustomer($id)
    {
        $this->apiDelete('/customers/' . $id);
        return redirect('/customers')->with('success', 'Client supprimé.');
    }
}
