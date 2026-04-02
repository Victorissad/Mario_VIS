@extends('layouts.dvd-layout')

@section('title', isset($film) ? 'Modifier le film - RFTG' : 'Ajouter un film - RFTG')

@section('content')
<div class="mb-3">
    <a href="/films" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Retour à la liste
    </a>
</div>

<div class="card shadow-lg">
    <div class="card-header {{ isset($film) ? 'bg-warning' : 'bg-success' }} text-white">
        <h2 class="mb-0">
            <i class="bi bi-{{ isset($film) ? 'pencil' : 'plus-circle' }}"></i>
            {{ isset($film) ? 'Modifier le film : ' . $film['title'] : 'Ajouter un film' }}
        </h2>
    </div>
    <div class="card-body">

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ isset($film) ? '/films/' . $film['filmId'] : '/films' }}">
            @csrf
            @if(isset($film))
                @method('PUT')
            @endif

            <div class="row">
                <div class="col-md-8 mb-3">
                    <label class="form-label fw-bold">Titre <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                           value="{{ old('title', $film['title'] ?? '') }}" required>
                    @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">Année de sortie</label>
                    <input type="number" name="releaseYear" class="form-control"
                           value="{{ old('releaseYear', $film['releaseYear'] ?? '') }}" min="1888" max="2099">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Description</label>
                <textarea name="description" class="form-control" rows="3">{{ old('description', $film['description'] ?? '') }}</textarea>
            </div>

            <div class="row">
                <div class="col-md-3 mb-3">
                    <label class="form-label fw-bold">Durée (minutes)</label>
                    <input type="number" name="length" class="form-control"
                           value="{{ old('length', $film['length'] ?? '') }}" min="1">
                </div>

                <div class="col-md-3 mb-3">
                    <label class="form-label fw-bold">Durée location (jours) <span class="text-danger">*</span></label>
                    <input type="number" name="rentalDuration" class="form-control @error('rentalDuration') is-invalid @enderror"
                           value="{{ old('rentalDuration', $film['rentalDuration'] ?? '') }}" min="1" required>
                    @error('rentalDuration') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <input type="hidden" name="rentalRate" value="{{ old('rentalRate', $film['rentalRate'] ?? '4.99') }}">
                <input type="hidden" name="replacementCost" value="{{ old('replacementCost', $film['replacementCost'] ?? '19.99') }}">
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">Classement</label>
                    <select name="rating" class="form-select">
                        <option value="">-- Sélectionner --</option>
                        @foreach(['G', 'PG', 'PG-13', 'R', 'NC-17'] as $r)
                            <option value="{{ $r }}" {{ old('rating', $film['rating'] ?? '') === $r ? 'selected' : '' }}>{{ $r }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-8 mb-3">
                    <label class="form-label fw-bold">Caractéristiques spéciales</label>
                    @php
                        $currentFeatures = old('specialFeatures', $film['specialFeatures'] ?? '');
                        $currentFeatures = is_array($currentFeatures) ? $currentFeatures : explode(',', $currentFeatures);
                        $currentFeatures = array_map('trim', $currentFeatures);
                    @endphp
                    @foreach(['Trailers', 'Commentaries', 'Deleted Scenes', 'Behind the Scenes'] as $feature)
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" name="specialFeatures[]"
                                   value="{{ $feature }}" id="sf_{{ $loop->index }}"
                                   {{ in_array($feature, $currentFeatures) ? 'checked' : '' }}>
                            <label class="form-check-label" for="sf_{{ $loop->index }}">{{ $feature }}</label>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="d-flex gap-2 mt-3">
                <button type="submit" class="btn btn-{{ isset($film) ? 'warning' : 'success' }}">
                    <i class="bi bi-check-circle"></i>
                    {{ isset($film) ? 'Enregistrer les modifications' : 'Ajouter le film' }}
                </button>
                <a href="/films" class="btn btn-secondary">Annuler</a>
            </div>
        </form>
    </div>
</div>
@endsection
