@extends('layouts.dvd-layout')

@section('title', 'Gestion du stock - RFTG')

@section('content')
<div class="row mb-4 align-items-center">
    <div class="col">
        <h1 class="display-4">
            <i class="bi bi-box text-success"></i> Gestion du stock
        </h1>
        @if(isset($inventories) && count($inventories) > 0)
            <p class="text-muted">Total : <strong>{{ count($inventories) }}</strong> exemplaires de DVD en stock</p>
        @endif
    </div>
    <div class="col-auto">
        <button class="btn btn-success" data-bs-toggle="collapse" data-bs-target="#formAjout">
            <i class="bi bi-plus-circle"></i> Ajouter un exemplaire
        </button>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="bi bi-check-circle"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show">
        @foreach($errors->all() as $error) <p class="mb-0">{{ $error }}</p> @endforeach
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<!-- Formulaire ajout exemplaire -->
<div class="collapse mb-4" id="formAjout">
    <div class="card border-success shadow-sm">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0"><i class="bi bi-plus-circle"></i> Ajouter un exemplaire au stock</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="/inventories" class="row g-3 align-items-end">
                @csrf
                <div class="col-md-4">
                    <label class="form-label fw-bold">ID du film <span class="text-danger">*</span></label>
                    <input type="number" name="filmId" class="form-control" placeholder="ex: 1" min="1" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">ID du magasin <span class="text-danger">*</span></label>
                    <input type="number" name="storeId" class="form-control" placeholder="ex: 1" min="1" required>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-success w-100">
                        <i class="bi bi-check-circle"></i> Ajouter
                    </button>
                </div>
            </form>
        </div>
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
                            <th style="width: 140px;">Actions</th>
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
                                    <div class="d-flex gap-1">
                                        <a href="/inventories/{{ $inventory['inventoryId'] }}" class="btn btn-sm btn-primary">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <form method="POST" action="/inventories/{{ $inventory['inventoryId'] }}"
                                              onsubmit="return confirm('Supprimer cet exemplaire du stock ?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
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
