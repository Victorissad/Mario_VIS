{{--
    Vue: Formulaire d'ajout d'exemplaires
    Route: GET /inventories/create

    Permet de créer plusieurs exemplaires identiques d'un film
--}}

@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-12">
            <h1>Ajouter des exemplaires</h1>
            <p class="text-muted">Créer plusieurs exemplaires identiques d'un film</p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    {{-- Formulaire POST vers /inventories --}}
                    <form action="{{ route('inventories.store') }}" method="POST">
                        @csrf

                        {{-- Sélection du film --}}
                        <div class="mb-3">
                            <label for="film_id" class="form-label">Film <span class="text-danger">*</span></label>
                            <select name="film_id" id="film_id" class="form-select @error('film_id') is-invalid @enderror" required>
                                <option value="">-- Sélectionnez un film --</option>
                                @foreach($films as $film)
                                    <option value="{{ $film['filmId'] }}" {{ old('film_id') == $film['filmId'] ? 'selected' : '' }}>
                                        {{ $film['title'] }}
                                        @if(isset($film['releaseYear']))
                                            ({{ $film['releaseYear'] }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('film_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Sélection du magasin --}}
                        <div class="mb-3">
                            <label for="store_id" class="form-label">Magasin <span class="text-danger">*</span></label>
                            <select name="store_id" id="store_id" class="form-select @error('store_id') is-invalid @enderror" required>
                                <option value="">-- Sélectionnez un magasin --</option>
                                @foreach($stores as $store)
                                    <option value="{{ $store['storeId'] }}" {{ old('store_id') == $store['storeId'] ? 'selected' : '' }}>
                                        Magasin #{{ $store['storeId'] }}
                                        @if(isset($store['staffMembers']) && count($store['staffMembers']) > 0)
                                            - {{ $store['staffMembers'][0]['firstName'] }} {{ $store['staffMembers'][0]['lastName'] }}
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('store_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Nombre d'exemplaires --}}
                        <div class="mb-3">
                            <label for="count" class="form-label">Nombre d'exemplaires <span class="text-danger">*</span></label>
                            <input type="number"
                                   name="count"
                                   id="count"
                                   class="form-control @error('count') is-invalid @enderror"
                                   value="{{ old('count', 1) }}"
                                   min="1"
                                   max="100"
                                   required>
                            <div class="form-text">Nombre d'exemplaires identiques à créer (minimum 1, maximum 100)</div>
                            @error('count')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Boutons --}}
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('inventories.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Annuler
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-plus-circle"></i> Ajouter
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Encadré d'information --}}
        <div class="col-md-4">
            <div class="card bg-light">
                <div class="card-body">
                    <h5 class="card-title">Information</h5>
                    <p class="card-text">
                        Chaque exemplaire créé aura un identifiant unique dans l'inventaire.
                    </p>
                    <p class="card-text">
                        Par exemple, si vous créez 5 exemplaires du film "Avatar" pour le magasin #1,
                        le système créera 5 entrées distinctes dans l'inventaire.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
