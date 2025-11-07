@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-pencil"></i> Modifier le film : {{ $film['title'] ?? 'Sans titre' }}
                    </h5>
                    <a href="{{ route('films.show', $film['filmId'] ?? $film['id']) }}" class="btn btn-secondary btn-sm">
                        <i class="bi bi-arrow-left"></i> Retour
                    </a>
                </div>

                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-triangle"></i>
                            <strong>Erreurs de validation :</strong>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('films.update', $film['filmId'] ?? $film['id']) }}">
                        @csrf
                        @method('PUT')

                        <div class="row mb-3">
                            <div class="col-md-8">
                                <label for="title" class="form-label">
                                    Titre <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       class="form-control @error('title') is-invalid @enderror"
                                       id="title"
                                       name="title"
                                       value="{{ old('title', $film['title'] ?? '') }}"
                                       required
                                       maxlength="255">
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="releaseYear" class="form-label">
                                    Année de sortie <span class="text-danger">*</span>
                                </label>
                                <input type="number"
                                       class="form-control @error('releaseYear') is-invalid @enderror"
                                       id="releaseYear"
                                       name="releaseYear"
                                       value="{{ old('releaseYear', $film['releaseYear'] ?? '') }}"
                                       required
                                       min="1888"
                                       max="{{ date('Y') + 5 }}">
                                @error('releaseYear')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description"
                                      name="description"
                                      rows="4">{{ old('description', $film['description'] ?? '') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="languageId" class="form-label">
                                    Langue <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('languageId') is-invalid @enderror"
                                        id="languageId"
                                        name="languageId"
                                        required>
                                    <option value="">-- Sélectionner --</option>
                                    <option value="1" {{ old('languageId', $film['languageId'] ?? '') == 1 ? 'selected' : '' }}>Anglais</option>
                                    <option value="2" {{ old('languageId', $film['languageId'] ?? '') == 2 ? 'selected' : '' }}>Français</option>
                                    <option value="3" {{ old('languageId', $film['languageId'] ?? '') == 3 ? 'selected' : '' }}>Espagnol</option>
                                    <option value="4" {{ old('languageId', $film['languageId'] ?? '') == 4 ? 'selected' : '' }}>Allemand</option>
                                    <option value="5" {{ old('languageId', $film['languageId'] ?? '') == 5 ? 'selected' : '' }}>Italien</option>
                                    <option value="6" {{ old('languageId', $film['languageId'] ?? '') == 6 ? 'selected' : '' }}>Japonais</option>
                                </select>
                                @error('languageId')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="length" class="form-label">Durée (minutes)</label>
                                <input type="number"
                                       class="form-control @error('length') is-invalid @enderror"
                                       id="length"
                                       name="length"
                                       value="{{ old('length', $film['length'] ?? '') }}"
                                       min="1">
                                @error('length')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="rating" class="form-label">Classification</label>
                                <select class="form-select @error('rating') is-invalid @enderror"
                                        id="rating"
                                        name="rating">
                                    <option value="">-- Sélectionner --</option>
                                    <option value="G" {{ old('rating', $film['rating'] ?? '') == 'G' ? 'selected' : '' }}>G - Tous publics</option>
                                    <option value="PG" {{ old('rating', $film['rating'] ?? '') == 'PG' ? 'selected' : '' }}>PG - Accord parental souhaité</option>
                                    <option value="PG-13" {{ old('rating', $film['rating'] ?? '') == 'PG-13' ? 'selected' : '' }}>PG-13 - Déconseillé -13 ans</option>
                                    <option value="R" {{ old('rating', $film['rating'] ?? '') == 'R' ? 'selected' : '' }}>R - Interdit -17 ans sans adulte</option>
                                    <option value="NC-17" {{ old('rating', $film['rating'] ?? '') == 'NC-17' ? 'selected' : '' }}>NC-17 - Interdit -17 ans</option>
                                </select>
                                @error('rating')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="replacementCost" class="form-label">Coût de remplacement (€)</label>
                                <input type="number"
                                       class="form-control @error('replacementCost') is-invalid @enderror"
                                       id="replacementCost"
                                       name="replacementCost"
                                       value="{{ old('replacementCost', $film['replacementCost'] ?? '') }}"
                                       step="0.01"
                                       min="0">
                                @error('replacementCost')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="specialFeatures" class="form-label">Caractéristiques spéciales</label>
                                <input type="text"
                                       class="form-control @error('specialFeatures') is-invalid @enderror"
                                       id="specialFeatures"
                                       name="specialFeatures"
                                       value="{{ old('specialFeatures', $film['specialFeatures'] ?? '') }}"
                                       placeholder="Ex: Commentaires, Scènes supprimées...">
                                @error('specialFeatures')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <small class="text-muted">
                                <i class="bi bi-info-circle"></i>
                                Les champs marqués d'un <span class="text-danger">*</span> sont obligatoires.
                            </small>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('films.show', $film['filmId'] ?? $film['id']) }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Annuler
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Enregistrer les modifications
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
