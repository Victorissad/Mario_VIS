<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Service pour gérer les appels API vers l'inventaire (exemplaires de DVD)
 *
 * Ce service communique avec l'API Spring Boot pour toutes les opérations
 * sur les exemplaires de films (inventory).
 */
class ToadInventoryService
{
    private string $baseUrl;

    public function __construct()
    {
        // Récupère l'URL de l'API depuis la config (config/services.php)
        $this->baseUrl = rtrim((string) config('services.toad.url', 'http://localhost:8180'), '/');
    }

    /**
     * Récupère tous les exemplaires de l'inventaire
     *
     * @return array|null Tableau d'exemplaires ou null en cas d'erreur
     *
     * Explication: Fait un GET sur /inventories pour récupérer tous les DVD
     * physiques de tous les magasins avec leurs informations de film.
     */
    public function getAllInventories(): ?array
    {
        $url = $this->baseUrl . '/inventories';

        try {
            $headers = ['Accept' => 'application/json'];

            Log::info('Appel API Inventories', ['url' => $url]);

            $response = Http::withHeaders($headers)
                ->timeout(10)
                ->get($url);

            if ($response->successful()) {
                return $response->json();
            }

            Log::warning('Inventories API KO', ['status' => $response->status(), 'body' => $response->body()]);
            return null;
        } catch (\Throwable $e) {
            Log::error('Erreur API Inventories', ['msg' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Récupère un exemplaire par son ID
     *
     * @param int $id L'ID de l'exemplaire
     * @return array|null Les données de l'exemplaire ou null si introuvable
     *
     * Explication: Permet d'obtenir les détails complets d'un DVD spécifique
     * avec toutes les infos du film associé.
     */
    public function getInventoryById(int $id): ?array
    {
        $url = $this->baseUrl . '/inventories/' . $id;

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
            Log::error('Erreur API Inventory', ['msg' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Crée plusieurs exemplaires identiques d'un film dans un magasin
     *
     * @param int $filmId L'ID du film
     * @param int $storeId L'ID du magasin
     * @param int $count Le nombre d'exemplaires à créer
     * @return array Tableau des exemplaires créés (peut être vide en cas d'erreur)
     *
     * Explication: Pour créer 5 DVD du même film, on fait 5 appels POST.
     * Chaque exemplaire créé aura un inventory_id unique.
     * Si une erreur survient, on arrête et on retourne ce qui a été créé.
     */
    public function createMultipleInventories(int $filmId, int $storeId, int $count): array
    {
        $created = [];

        // Boucle pour créer N exemplaires
        for ($i = 0; $i < $count; $i++) {
            $inventory = $this->createInventory($filmId, $storeId);

            if ($inventory) {
                $created[] = $inventory;
            } else {
                // En cas d'erreur, on arrête la création
                Log::warning('Arrêt création multiple', ['créés' => count($created), 'demandés' => $count]);
                break;
            }
        }

        return $created;
    }

    /**
     * Crée un seul exemplaire (méthode privée utilisée par createMultipleInventories)
     *
     * @param int $filmId L'ID du film
     * @param int $storeId L'ID du magasin
     * @return array|null L'exemplaire créé ou null en cas d'erreur
     */
    private function createInventory(int $filmId, int $storeId): ?array
    {
        $url = $this->baseUrl . '/inventories';

        try {
            $headers = [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ];

            // Données à envoyer : juste le film et le magasin
            $data = [
                'filmId' => $filmId,
                'storeId' => $storeId
            ];

            Log::info('Création inventory', ['url' => $url, 'data' => $data]);

            $response = Http::withHeaders($headers)
                ->timeout(10)
                ->post($url, $data);

            if ($response->successful()) {
                Log::info('Inventory créé', ['response' => $response->json()]);
                return $response->json();
            }

            Log::warning('Création inventory KO', ['status' => $response->status(), 'body' => $response->body()]);
            return null;
        } catch (\Throwable $e) {
            Log::error('Erreur création inventory', ['msg' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Met à jour un exemplaire (changement de magasin uniquement)
     *
     * @param int $id L'ID de l'exemplaire à modifier
     * @param int $filmId L'ID du film (ne change pas, mais obligatoire pour l'API)
     * @param int $storeId Le nouveau magasin
     * @return array|null L'exemplaire mis à jour ou null en cas d'erreur
     *
     * Explication: On ne peut modifier que le magasin (déplacer un DVD).
     * Le film reste le même, mais on doit quand même l'envoyer dans la requête.
     */
    public function updateInventory(int $id, int $filmId, int $storeId): ?array
    {
        $url = $this->baseUrl . '/inventories/' . $id;

        try {
            $headers = [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ];

            $data = [
                'filmId' => $filmId,    // Le film ne change pas
                'storeId' => $storeId   // Le nouveau magasin
            ];

            Log::info('Mise à jour inventory', ['url' => $url, 'id' => $id, 'data' => $data]);

            $response = Http::withHeaders($headers)
                ->timeout(10)
                ->put($url, $data);

            if ($response->successful()) {
                Log::info('Inventory mis à jour', ['response' => $response->json()]);
                return $response->json();
            }

            Log::warning('Mise à jour inventory KO', ['status' => $response->status(), 'body' => $response->body()]);
            return null;
        } catch (\Throwable $e) {
            Log::error('Erreur mise à jour inventory', ['msg' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Supprime un exemplaire
     *
     * @param int $id L'ID de l'exemplaire à supprimer
     * @return bool true si succès, false sinon
     * @throws \Exception Si l'exemplaire est en location (erreur 500 avec constraint)
     *
     * Explication: La suppression échoue si le DVD est loué (foreign key constraint).
     * Dans ce cas, on lance une Exception avec un message clair.
     */
    public function deleteInventory(int $id): bool
    {
        $url = $this->baseUrl . '/inventories/' . $id;

        try {
            $headers = ['Accept' => 'application/json'];

            Log::info('Suppression inventory', ['url' => $url, 'id' => $id]);

            $response = Http::withHeaders($headers)
                ->timeout(10)
                ->delete($url);

            if ($response->successful()) {
                Log::info('Inventory supprimé', ['id' => $id]);
                return true;
            }

            // Si erreur 500 avec "constraint", c'est que le DVD est loué
            if ($response->status() === 500) {
                $body = $response->body();
                if (str_contains($body, 'foreign key constraint fails')) {
                    throw new \Exception('Impossible de supprimer cet exemplaire : il est actuellement en location.');
                }
            }

            Log::warning('Suppression inventory KO', ['status' => $response->status(), 'body' => $response->body()]);
            return false;
        } catch (\Exception $e) {
            // Relancer l'exception pour le contrôleur
            throw $e;
        } catch (\Throwable $e) {
            Log::error('Erreur suppression inventory', ['msg' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Vérifie le statut de location d'un exemplaire
     *
     * @param int $id L'ID de l'exemplaire
     * @return array|null Les infos de statut ou null en cas d'erreur
     *
     * Explication: Appelle le nouveau endpoint /inventories/{id}/rental-status
     * pour savoir si le DVD est "Disponible" ou "Loué".
     *
     * Retour attendu:
     * {
     *   "inventoryId": 1,
     *   "isRented": true,
     *   "isAvailable": false,
     *   "status": "Loué"
     * }
     */
    public function getInventoryRentalStatus(int $id): ?array
    {
        $url = $this->baseUrl . '/inventories/' . $id . '/rental-status';

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
            Log::error('Erreur API Rental Status', ['msg' => $e->getMessage()]);
            return null;
        }
    }
}
