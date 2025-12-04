@extends('layouts.dvd-layout')

@section('title', 'Gestion de l\'inventaire - DVD Rental')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h1 class="display-4">
            <i class="bi bi-box text-success"></i> Gestion de l'inventaire
        </h1>
        @if(isset($inventories) && count($inventories) > 0)
            <p class="text-muted">Total : <strong>{{ count($inventories) }}</strong> exemplaires de DVD en stock</p>
        @endif
    </div>
</div>

@if(isset($inventories) && count($inventories) > 0)
    <div class="card shadow">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th style="width: 100px;">ID Inventaire</th>
                            <th style="width: 100px;">ID Film</th>
                            <th style="width: 150px;">ID Magasin</th>
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
                                    <span class="badge bg-success">
                                        <i class="bi bi-shop"></i> Magasin #{{ $inventory['storeId'] ?? '-' }}
                                    </span>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        <i class="bi bi-clock-history"></i>
                                        {{ $inventory['lastUpdate'] ?? '-' }}
                                    </small>
                                </td>
                                <td>
                                    <a href="/inventories/{{ $inventory['inventoryId'] }}" class="btn btn-sm btn-primary">
                                        <i class="bi bi-eye"></i> Détails
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@else
    <div class="alert alert-warning" role="alert">
        <i class="bi bi-exclamation-triangle"></i> Aucun inventaire trouvé.
    </div>
@endif
@endsection
