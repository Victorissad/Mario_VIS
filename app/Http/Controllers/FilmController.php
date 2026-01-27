<?php

namespace App\Http\Controllers;

use App\Services\ToadFilmService;
use Illuminate\Http\Request;

class FilmController extends Controller
{
    private ToadFilmService $filmService;

    public function __construct(ToadFilmService $filmService)
    {
        $this->middleware('auth');
        $this->filmService = $filmService;
    }

    public function index()
    {
        $films = $this->filmService->getAllFilms();

        return view('films.index', [
            'films' => $films ?? []
        ]);
    }

    public function show($id)
    {
        $film = $this->filmService->getFilmById($id);

        if (!$film) {
            abort(404, 'Film non trouvé');
        }

        return view('films.show', [
            'film' => $film
        ]);
    }

    /**
     * Affiche le formulaire de création d'un nouveau film
     */
    public function create()
    {
        return view('films.create');
    }

    /**
     * Enregistre un nouveau film dans la base de données
     */
    public function store(Request $request)
    {
        // Validation des données
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'releaseYear' => 'required|integer|min:1888|max:' . (date('Y') + 5),
            'languageId' => 'required|integer|min:1',
            'length' => 'nullable|integer|min:1',
            'replacementCost' => 'nullable|numeric|min:0',
            'rating' => 'nullable|string|in:G,PG,PG-13,R,NC-17',
            'specialFeatures' => 'nullable|string'
        ], [
            'title.required' => 'Le titre est obligatoire',
            'title.max' => 'Le titre ne peut pas dépasser 255 caractères',
            'releaseYear.required' => 'L\'année de sortie est obligatoire',
            'releaseYear.integer' => 'L\'année de sortie doit être un nombre entier',
            'releaseYear.min' => 'L\'année de sortie ne peut pas être antérieure à 1888',
            'releaseYear.max' => 'L\'année de sortie ne peut pas être dans plus de 5 ans',
            'languageId.required' => 'La langue est obligatoire',
            'length.integer' => 'La durée doit être un nombre entier',
            'length.min' => 'La durée doit être au moins 1 minute',
            'replacementCost.numeric' => 'Le coût de remplacement doit être un nombre',
            'replacementCost.min' => 'Le coût de remplacement ne peut pas être négatif',
            'rating.in' => 'La note doit être G, PG, PG-13, R ou NC-17'
        ]);

        // Création du film via l'API
        $film = $this->filmService->createFilm($validated);

        if ($film) {
            return redirect()
                ->route('films.index')
                ->with('success', 'Le film a été créé avec succès.');
        }

        return back()
            ->withInput()
            ->with('error', 'Une erreur est survenue lors de la création du film.');
    }

    /**
     * Affiche le formulaire d'édition d'un film
     */
    public function edit($id)
    {
        $film = $this->filmService->getFilmById($id);

        if (!$film) {
            abort(404, 'Film non trouvé');
        }

        return view('films.edit', [
            'film' => $film
        ]);
    }

    /**
     * Met à jour un film existant
     */
    public function update(Request $request, $id)
    {
        // Validation des données
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'releaseYear' => 'required|integer|min:1888|max:' . (date('Y') + 5),
            'languageId' => 'required|integer|min:1',
            'length' => 'nullable|integer|min:1',
            'replacementCost' => 'nullable|numeric|min:0',
            'rating' => 'nullable|string|in:G,PG,PG-13,R,NC-17',
            'specialFeatures' => 'nullable|string'
        ], [
            'title.required' => 'Le titre est obligatoire',
            'title.max' => 'Le titre ne peut pas dépasser 255 caractères',
            'releaseYear.required' => 'L\'année de sortie est obligatoire',
            'releaseYear.integer' => 'L\'année de sortie doit être un nombre entier',
            'releaseYear.min' => 'L\'année de sortie ne peut pas être antérieure à 1888',
            'releaseYear.max' => 'L\'année de sortie ne peut pas être dans plus de 5 ans',
            'languageId.required' => 'La langue est obligatoire',
            'length.integer' => 'La durée doit être un nombre entier',
            'length.min' => 'La durée doit être au moins 1 minute',
            'replacementCost.numeric' => 'Le coût de remplacement doit être un nombre',
            'replacementCost.min' => 'Le coût de remplacement ne peut pas être négatif',
            'rating.in' => 'La note doit être G, PG, PG-13, R ou NC-17'
        ]);

        // Mise à jour du film via l'API
        $film = $this->filmService->updateFilm($id, $validated);

        if ($film) {
            return redirect()
                ->route('films.show', $id)
                ->with('success', 'Le film a été mis à jour avec succès.');
        }

        return back()
            ->withInput()
            ->with('error', 'Une erreur est survenue lors de la mise à jour du film.');
    }

    /**
     * Supprime un film
     */
    public function destroy($id)
    {
        $success = $this->filmService->deleteFilm($id);

        if ($success) {
            return redirect()
                ->route('films.index')
                ->with('success', 'Le film a été supprimé avec succès.');
        }

        return back()
            ->with('error', 'Une erreur est survenue lors de la suppression du film.');
    }
}