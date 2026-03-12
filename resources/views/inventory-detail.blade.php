@extends('layouts.dvd-layout')

@section('title', 'Exemplaire DVD #' . ($inventory['inventoryId'] ?? 'N/A'))

@section('content')
<div class="mb-3">
    <a href="/inventories" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Retour à l'inventaire
    </a>
</div>

<div class="card shadow-lg">
    <div class="card-header bg-success text-white">
        <h2 class="mb-0">
            <i class="bi bi-box"></i> Exemplaire DVD #{{ $inventory['inventoryId'] ?? 'N/A' }}
        </h2>
    </div>
    <div class="card-body">
        <div class="row">
            <!-- Informations de l'inventaire -->
            <div class="col-md-4">
                <div class="card bg-light mb-3">
                    <div class="card-body">
                        <h5 class="card-title text-center mb-4">
                            <i class="bi bi-info-circle"></i> Informations
                        </h5>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                                <span class="text-muted"><i class="bi bi-hash"></i> ID Inventaire</span>
                                <strong>#{{ $inventory['inventoryId'] ?? '-' }}</strong>
                            </div>

                            <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                                <span class="text-muted"><i class="bi bi-film"></i> ID Film</span>
                                <span class="badge bg-info">{{ $inventory['filmId'] ?? '-' }}</span>
                            </div>

                            <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                                <span class="text-muted"><i class="bi bi-shop"></i> ID Magasin</span>
                                <span class="badge bg-success">{{ $inventory['storeId'] ?? '-' }}</span>
                            </div>

                            <div class="d-flex justify-content-between align-items-center pb-2">
                                <span class="text-muted"><i class="bi bi-clock-history"></i> MAJ</span>
                                <small>{{ $inventory['lastUpdate'] ?? '-' }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informations du film et du magasin -->
            <div class="col-md-8">
                @if($film)
                    <div class="card border-primary mb-3">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="bi bi-film"></i> Informations du film
                            </h5>
                        </div>
                        <div class="card-body">
                            <h4 class="card-title">{{ $film['title'] ?? 'Sans titre' }}</h4>
                            <p class="card-text">{{ $film['description'] ?? 'Aucune description disponible.' }}</p>

                            <div class="row mt-4">
                                <div class="col-md-6">
                                    <ul class="list-unstyled">
                                        <li class="mb-2">
                                            <strong><i class="bi bi-calendar"></i> Année :</strong>
                                            <span class="badge bg-secondary">{{ $film['releaseYear'] ?? '-' }}</span>
                                        </li>
                                        <li class="mb-2">
                                            <strong><i class="bi bi-clock"></i> Durée :</strong>
                                            {{ $film['length'] ?? '-' }} minutes
                                        </li>
                                        <li class="mb-2">
                                            <strong><i class="bi bi-tag"></i> Classement :</strong>
                                            @if(isset($film['rating']))
                                                @php
                                                    $badgeColor = match($film['rating']) {
                                                        'G' => 'success',
                                                        'PG' => 'info',
                                                        'PG-13' => 'warning',
                                                        'R' => 'danger',
                                                        'NC-17' => 'dark',
                                                        default => 'secondary'
                                                    };
                                                @endphp
                                                <span class="badge bg-{{ $badgeColor }}">{{ $film['rating'] }}</span>
                                            @else
                                                -
                                            @endif
                                        </li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <ul class="list-unstyled">
                                        <li class="mb-2">
                                            <strong><i class="bi bi-calendar-week"></i> Durée location :</strong>
                                            {{ $film['rentalDuration'] ?? '-' }} jours
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <div class="mt-3">
                                <a href="/films/{{ $film['filmId'] }}" class="btn btn-primary btn-sm">
                                    <i class="bi bi-eye"></i> Voir la fiche complète du film
                                </a>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i> Impossible de charger les informations du film.
                    </div>
                @endif

                @if($store)
                    <div class="card border-info">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0">
                                <i class="bi bi-shop"></i> Magasin où se trouve cet exemplaire
                            </h5>
                        </div>
                        <div class="card-body">
                            <h5>Magasin #{{ $store['storeId'] ?? '-' }}</h5>
                            <ul class="list-unstyled">
                                <li><strong><i class="bi bi-person-badge"></i> Manager :</strong> Staff #{{ $store['managerStaffId'] ?? '-' }}</li>
                                <li><strong><i class="bi bi-geo-alt"></i> Adresse :</strong> ID #{{ $store['addressId'] ?? '-' }}</li>
                            </ul>

                            <div class="mt-3">
                                <a href="/stores/{{ $store['storeId'] }}" class="btn btn-info btn-sm text-white">
                                    <i class="bi bi-eye"></i> Voir les détails du magasin
                                </a>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="alert alert-warning mt-3">
                        <i class="bi bi-exclamation-triangle"></i> Impossible de charger les informations du magasin.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
