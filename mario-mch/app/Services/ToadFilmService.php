<?php


namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ToadFilmService
{
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = rtrim((string) config('services.toad.url', 'http://localhost:8180'), '/');
    }

    public function getAllFilms(): ?array
    {
        $url = $this->baseUrl . '/films';

        try {
            $headers = ['Accept' => 'application/json'];
            
            // Récupère le token JWT depuis la session
            $token = $this->getUserToken();
            if ($token) {
                $headers['Authorization'] = "Bearer {$token}";
            }

            Log::info('Appel API Films', ['url' => $url, 'has_token' => !empty($token)]);

            $response = Http::withHeaders($headers)
                ->timeout(10)
                ->get($url);

            if ($response->successful()) {
                return $response->json();
            }

            Log::warning('Films API KO', ['status' => $response->status(), 'body' => $response->body()]);
            return null;
        } catch (\Throwable $e) {
            Log::error('Erreur API Films', ['msg' => $e->getMessage()]);
            return null;
        }
    }

    public function getFilmById(int $id): ?array
    {
        $url = $this->baseUrl . '/films/' . $id;

        try {
            $headers = ['Accept' => 'application/json'];
            $token = $this->getUserToken();
            if ($token) {
                $headers['Authorization'] = "Bearer {$token}";
            }

            $response = Http::withHeaders($headers)
                ->timeout(10)
                ->get($url);

            if ($response->successful()) {
                return $response->json();
            }

            return null;
        } catch (\Throwable $e) {
            Log::error('Erreur API Film', ['msg' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Crée un nouveau film
     */
    public function createFilm(array $filmData): ?array
    {
        $url = $this->baseUrl . '/films';

        try {
            $headers = ['Accept' => 'application/json', 'Content-Type' => 'application/json'];
            $token = $this->getUserToken();
            if ($token) {
                $headers['Authorization'] = "Bearer {$token}";
            }

            // Transform field names to match API expectations
            $apiData = $this->transformToApiFormat($filmData);

            Log::info('Création film', ['url' => $url, 'data' => $apiData]);

            $response = Http::withHeaders($headers)
                ->timeout(10)
                ->post($url, $apiData);

            if ($response->successful()) {
                Log::info('Film créé avec succès', ['response' => $response->json()]);
                return $response->json();
            }

            Log::warning('Création film KO', ['status' => $response->status(), 'body' => $response->body()]);
            return null;
        } catch (\Throwable $e) {
            Log::error('Erreur création film', ['msg' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Met à jour un film existant
     */
    public function updateFilm(int $id, array $filmData): ?array
    {
        $url = $this->baseUrl . '/films/' . $id;

        try {
            $headers = ['Accept' => 'application/json', 'Content-Type' => 'application/json'];
            $token = $this->getUserToken();
            if ($token) {
                $headers['Authorization'] = "Bearer {$token}";
            }

            // Transform field names to match API expectations
            $apiData = $this->transformToApiFormat($filmData);

            Log::info('Mise à jour film', ['url' => $url, 'id' => $id, 'data' => $apiData]);

            $response = Http::withHeaders($headers)
                ->timeout(10)
                ->put($url, $apiData);

            if ($response->successful()) {
                Log::info('Film mis à jour avec succès', ['response' => $response->json()]);
                return $response->json();
            }

            Log::warning('Mise à jour film KO', ['status' => $response->status(), 'body' => $response->body()]);
            return null;
        } catch (\Throwable $e) {
            Log::error('Erreur mise à jour film', ['msg' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Supprime un film
     */
    public function deleteFilm(int $id): bool
    {
        $url = $this->baseUrl . '/films/' . $id;

        try {
            $headers = ['Accept' => 'application/json'];
            $token = $this->getUserToken();
            if ($token) {
                $headers['Authorization'] = "Bearer {$token}";
            }

            Log::info('Suppression film', ['url' => $url, 'id' => $id]);

            $response = Http::withHeaders($headers)
                ->timeout(10)
                ->delete($url);

            if ($response->successful()) {
                Log::info('Film supprimé avec succès', ['id' => $id]);
                return true;
            }

            Log::warning('Suppression film KO', ['status' => $response->status(), 'body' => $response->body()]);
            return false;
        } catch (\Throwable $e) {
            Log::error('Erreur suppression film', ['msg' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Transforme les données du format Laravel vers le format API
     */
    private function transformToApiFormat(array $filmData): array
    {
        $apiData = [];

        // Map languageId to originalLanguageId
        if (isset($filmData['languageId'])) {
            $apiData['originalLanguageId'] = $filmData['languageId'];
        }

        // Copy other fields as-is
        $fieldsToKeep = ['title', 'description', 'releaseYear', 'length', 'replacementCost', 'rating', 'specialFeatures'];
        foreach ($fieldsToKeep as $field) {
            if (isset($filmData[$field])) {
                $apiData[$field] = $filmData[$field];
            }
        }

        // Add default values for required fields if not present
        if (!isset($apiData['rentalDuration'])) {
            $apiData['rentalDuration'] = 3;
        }
        if (!isset($apiData['rentalRate'])) {
            $apiData['rentalRate'] = 4.99;
        }
        if (!isset($apiData['replacementCost'])) {
            $apiData['replacementCost'] = 19.99;
        }

        return $apiData;
    }

    /**
     * Récupère le token JWT depuis la session utilisateur
     */
    private function getUserToken(): ?string
    {
        $userData = session('toad_user');
        Log::info('Récupération token utilisateur', ['userData' => $userData]);

        return $userData['token'] ?? null;
    }
}