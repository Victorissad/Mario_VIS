<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Service pour gérer les appels API vers les magasins (stores)
 *
 * Ce service communique avec l'API Spring Boot pour toutes les opérations
 * sur les magasins de DVD.
 */
class ToadStoreService
{
    private string $baseUrl;

    public function __construct()
    {
        // Récupère l'URL de l'API depuis la config
        $this->baseUrl = rtrim((string) config('services.toad.url', 'http://localhost:8180'), '/');
    }

    /**
     * Récupère tous les magasins
     *
     * @return array|null Tableau de magasins ou null en cas d'erreur
     *
     * Explication: Récupère la liste de tous les magasins avec leurs
     * responsables (staffMembers). L'API fait déjà un JOIN optimisé.
     */
    public function getAllStores(): ?array
    {
        $url = $this->baseUrl . '/stores';

        try {
            $headers = ['Accept' => 'application/json'];

            Log::info('Appel API Stores', ['url' => $url]);

            $response = Http::withHeaders($headers)
                ->timeout(10)
                ->get($url);

            if ($response->successful()) {
                return $response->json();
            }

            Log::warning('Stores API KO', ['status' => $response->status(), 'body' => $response->body()]);
            return null;
        } catch (\Throwable $e) {
            Log::error('Erreur API Stores', ['msg' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Récupère un magasin par son ID
     *
     * @param int $id L'ID du magasin
     * @return array|null Les données du magasin ou null si introuvable
     *
     * Explication: Récupère les détails complets d'un magasin avec
     * les informations du responsable.
     */
    public function getStoreById(int $id): ?array
    {
        $url = $this->baseUrl . '/stores/' . $id;

        try {
            $headers = ['Accept' => 'application/json'];

            $response = Http::withHeaders($headers)
                ->timeout(10)
                ->get($url);

            if ($response->successful()) {
                return $response->json();
            }

            return null;
        } catch (\Throwable $e) {
            Log::error('Erreur API Store', ['msg' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Récupère les statistiques d'un magasin
     *
     * @param int $storeId L'ID du magasin
     * @return array Les statistiques (total, available, rented)
     *
     * Explication: Appelle le nouveau endpoint /stores/{id}/statistics
     * qui calcule automatiquement:
     * - total: nombre total d'exemplaires
     * - available: nombre de DVD disponibles
     * - rented: nombre de DVD loués
     *
     * Retour attendu:
     * {
     *   "storeId": 1,
     *   "total": 245,
     *   "available": 198,
     *   "rented": 47
     * }
     */
    public function getStoreStatistics(int $storeId): array
    {
        $url = $this->baseUrl . '/stores/' . $storeId . '/statistics';

        try {
            $headers = ['Accept' => 'application/json'];

            Log::info('Appel API Store Statistics', ['url' => $url]);

            $response = Http::withHeaders($headers)
                ->timeout(10)
                ->get($url);

            if ($response->successful()) {
                return $response->json();
            }

            // Si l'API ne répond pas, retourner des stats vides
            Log::warning('Store Statistics API KO', ['status' => $response->status()]);
            return [
                'storeId' => $storeId,
                'total' => 0,
                'available' => 0,
                'rented' => 0
            ];
        } catch (\Throwable $e) {
            Log::error('Erreur API Store Statistics', ['msg' => $e->getMessage()]);
            // Retourner des stats vides en cas d'erreur
            return [
                'storeId' => $storeId,
                'total' => 0,
                'available' => 0,
                'rented' => 0
            ];
        }
    }
}
