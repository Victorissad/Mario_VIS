@extends('layouts.dvd-layout')

@section('title', ($film['title'] ?? 'Film') . ' - Détail')

@section('content')
<div class="mb-3">
    <a href="/films" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Retour à la liste
    </a>
</div>

<div class="card shadow-lg">
    <div class="card-header bg-primary text-white">
        <h2 class="mb-0">
            <i class="bi bi-film"></i> {{ $film['title'] ?? 'Sans titre' }}
        </h2>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-8">
                <h5 class="text-muted mb-3">Description</h5>
                <p class="lead">{{ $film['description'] ?? 'Aucune description disponible.' }}</p>

                @if(isset($film['specialFeatures']))
                    <div class="alert alert-info mt-4">
                        <strong><i class="bi bi-star-fill"></i> Fonctionnalités spéciales :</strong>
                        <p class="mb-0 mt-2">{{ $film['specialFeatures'] }}</p>
                    </div>
                @endif
            </div>

            <div class="col-md-4">
                <div class="card bg-light">
                    <div class="card-body">
                        <h5 class="card-title text-center mb-4">Informations</h5>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                                <span class="text-muted"><i class="bi bi-hash"></i> ID</span>
                                <strong>#{{ $film['filmId'] ?? '-' }}</strong>
                            </div>

                            <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                                <span class="text-muted"><i class="bi bi-calendar"></i> Année</span>
                                <span class="badge bg-secondary">{{ $film['releaseYear'] ?? '-' }}</span>
                            </div>

                            <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                                <span class="text-muted"><i class="bi bi-clock"></i> Durée</span>
                                <strong>{{ $film['length'] ?? '-' }} min</strong>
                            </div>

                            <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                                <span class="text-muted"><i class="bi bi-translate"></i> Langue</span>
                                <strong>ID {{ $film['originalLanguageId'] ?? '-' }}</strong>
                            </div>

                            <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                                <span class="text-muted"><i class="bi bi-tag"></i> Classement</span>
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
                                    <span>-</span>
                                @endif
                            </div>
                        </div>

                        <h6 class="text-center mt-4 mb-3">Tarification</h6>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                                <span class="text-muted"><i class="bi bi-calendar-week"></i> Durée location</span>
                                <strong>{{ $film['rentalDuration'] ?? '-' }} jours</strong>
                            </div>

                            <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                                <span class="text-muted"><i class="bi bi-currency-euro"></i> Tarif location</span>
                                @if(isset($film['rentalRate']))
                                    <span class="text-success fw-bold">{{ number_format($film['rentalRate'], 2) }} €</span>
                                @else
                                    <span>-</span>
                                @endif
                            </div>

                            <div class="d-flex justify-content-between align-items-center pb-2">
                                <span class="text-muted"><i class="bi bi-box-seam"></i> Coût remplacement</span>
                                @if(isset($film['replacementCost']))
                                    <span class="text-danger fw-bold">{{ number_format($film['replacementCost'], 2) }} €</span>
                                @else
                                    <span>-</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                @if(isset($film['lastUpdate']))
                    <div class="text-center mt-3">
                        <small class="text-muted">
                            <i class="bi bi-clock-history"></i>
                            Dernière mise à jour : {{ $film['lastUpdate'] }}
                        </small>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
