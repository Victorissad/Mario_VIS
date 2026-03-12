@extends('layouts.dvd-layout')

@section('title', 'Liste des magasins -RFTG')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h1 class="display-4">
            <i class="bi bi-shop text-info"></i> Liste des magasins
        </h1>
        @if(isset($stores) && count($stores) > 0)
            <p class="text-muted">Total : <strong>{{ count($stores) }}</strong> magasins</p>
        @endif
    </div>
</div>

@if(isset($stores) && count($stores) > 0)
    <div class="row">
        @foreach($stores as $store)
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">
                            <i class="bi bi-shop"></i> Magasin #{{ $store['storeId'] ?? '-' }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                                <span class="text-muted">
                                    <i class="bi bi-person-badge"></i> Manager
                                </span>
                                <span class="badge bg-primary">Staff #{{ $store['managerStaffId'] ?? '-' }}</span>
                            </div>

                            <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                                <span class="text-muted">
                                    <i class="bi bi-geo-alt"></i> Adresse
                                </span>
                                <span class="badge bg-secondary">ID #{{ $store['addressId'] ?? '-' }}</span>
                            </div>

                            <div class="d-flex justify-content-between align-items-center pb-2">
                                <span class="text-muted">
                                    <i class="bi bi-clock-history"></i> Dernière MAJ
                                </span>
                                <small class="text-muted">{{ $store['lastUpdate'] ?? '-' }}</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-light">
                        <a href="/stores/{{ $store['storeId'] }}" class="btn btn-primary btn-sm w-100">
                            <i class="bi bi-eye"></i> Voir les détails
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@else
    <div class="alert alert-warning" role="alert">
        <i class="bi bi-exclamation-triangle"></i> Aucun magasin trouvé.
    </div>
@endif
@endsection
