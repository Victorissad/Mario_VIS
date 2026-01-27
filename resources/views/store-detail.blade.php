@extends('layouts.dvd-layout')

@section('title', 'Magasin #' . ($store['storeId'] ?? 'N/A'))

@section('content')
<div class="mb-3">
    <a href="/stores" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Retour aux magasins
    </a>
</div>

<div class="card shadow-lg mb-4">
    <div class="card-header bg-info text-white">
        <h2 class="mb-0">
            <i class="bi bi-shop"></i> Magasin #{{ $store['storeId'] ?? 'N/A' }}
        </h2>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h5 class="mb-3"><i class="bi bi-info-circle"></i> Informations générales</h5>
                <ul class="list-group">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-hash"></i> ID Magasin</span>
                        <strong>#{{ $store['storeId'] ?? '-' }}</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-person-badge"></i> Manager (Staff ID)</span>
                        <span class="badge bg-primary">{{ $store['managerStaffId'] ?? '-' }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-geo-alt"></i> Adresse (ID)</span>
                        <span class="badge bg-secondary">{{ $store['addressId'] ?? '-' }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-clock-history"></i> Dernière mise à jour</span>
                        <small class="text-muted">{{ $store['lastUpdate'] ?? '-' }}</small>
                    </li>
                </ul>
            </div>

            <div class="col-md-6">
                <h5 class="mb-3"><i class="bi bi-bar-chart"></i> Statistiques</h5>
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <h1 class="display-3">{{ count($inventories) }}</h1>
                        <p class="mb-0">Exemplaires de DVD en stock</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Liste des inventaires du magasin -->
<div class="card shadow">
    <div class="card-header bg-dark text-white">
        <h4 class="mb-0">
            <i class="bi bi-box"></i> Inventaire du magasin ({{ count($inventories) }} exemplaires)
        </h4>
    </div>
    <div class="card-body p-0">
        @if(count($inventories) > 0)
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 100px;">ID Inventaire</th>
                            <th style="width: 100px;">ID Film</th>
                            <th>Dernière mise à jour</th>
                            <th style="width: 120px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($inventories as $inventory)
                            <tr>
                                <td>
                                    <span class="badge bg-primary">#{{ $inventory['inventoryId'] ?? '-' }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-info">Film #{{ $inventory['filmId'] ?? '-' }}</span>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        <i class="bi bi-clock-history"></i>
                                        {{ $inventory['lastUpdate'] ?? '-' }}
                                    </small>
                                </td>
                                <td>
                                    <a href="/inventories/{{ $inventory['inventoryId'] }}" class="btn btn-sm btn-primary">
                                        <i class="bi bi-eye"></i> Voir
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="alert alert-info m-3">
                <i class="bi bi-info-circle"></i> Aucun exemplaire de DVD dans ce magasin.
            </div>
        @endif
    </div>
</div>
@endsection
