@extends('layouts.dvd-layout')

@section('title', 'Gestion du stock - RFTG')

@section('content')
<div class="row mb-4 align-items-center">
    <div class="col">
        <h1 class="display-4">
            <i class="bi bi-box text-success"></i> Gestion du stock
        </h1>
        @if(isset($totalItems))
            <p class="text-muted">Total : <strong>{{ $totalItems }}</strong> exemplaires de DVD en stock</p>
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

@if(!empty($grouped))
    <div class="card shadow">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Titre du film</th>
                            <th>Magasin</th>
                            <th>Exemplaires</th>
                            <th>Note</th>
                            <th style="width: 120px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($grouped as $i => $row)
                            {{-- Ligne groupée --}}
                            <tr>
                                <td><strong>{{ $row['title'] }}</strong></td>
                                <td>
                                    <span class="badge bg-success">
                                        <i class="bi bi-shop"></i> Magasin #{{ $row['storeId'] }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-primary fs-6">{{ $row['count'] }}</span>
                                </td>
                                <td>
                                    @if($row['rating'])
                                        <span class="badge bg-info">{{ $row['rating'] }}</span>
                                    @else
                                        <span class="badge bg-secondary">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    {{-- Bouton pour déplier les exemplaires individuels --}}
                                    <button class="btn btn-sm btn-outline-secondary"
                                            data-bs-toggle="collapse"
                                            data-bs-target="#copies-{{ $i }}"
                                            title="Voir les exemplaires">
                                        <i class="bi bi-list-ul"></i> Gérer
                                    </button>
                                </td>
                            </tr>

                            {{-- Ligne dépliable : exemplaires individuels --}}
                            <tr class="collapse" id="copies-{{ $i }}">
                                <td colspan="5" class="bg-light p-0">
                                    <table class="table table-sm mb-0">
                                        <thead>
                                            <tr class="table-secondary">
                                                <th class="ps-4">ID Exemplaire</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($row['copies'] as $inventoryId)
                                                <tr>
                                                    <td class="ps-4">
                                                        <span class="badge bg-secondary">#{{ $inventoryId }}</span>
                                                    </td>
                                                    <td>
                                                        <a href="/inventories/{{ $inventoryId }}"
                                                           class="btn btn-sm btn-primary" title="Voir">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                        <button class="btn btn-sm btn-warning" title="Modifier le magasin"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#editModal"
                                                                data-id="{{ $inventoryId }}"
                                                                data-store="{{ $row['storeId'] }}">
                                                            <i class="bi bi-pencil"></i>
                                                        </button>
                                                        <form method="POST" action="/inventories/{{ $inventoryId }}"
                                                              style="display:inline;"
                                                              onsubmit="return confirm('Supprimer cet exemplaire du stock ?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger" title="Supprimer">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <p class="text-muted mt-3">
        <i class="bi bi-info-circle"></i>
        <strong>{{ count($grouped) }}</strong> titre(s) &mdash;
        <strong>{{ $totalItems }}</strong> exemplaire(s) au total
    </p>
@else
    <div class="alert alert-warning" role="alert">
        <i class="bi bi-exclamation-triangle"></i> Aucun inventaire trouvé.
    </div>
@endif
<!-- Modal modifier magasin -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-pencil"></i> Modifier le magasin</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <label class="form-label fw-bold">Nouveau magasin <span class="text-danger">*</span></label>
                    <input type="number" name="storeId" id="editStoreId" class="form-control" min="1" required>
                    <small class="text-muted">Exemplaire #<span id="editInventoryId"></span></small>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-warning">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('editModal').addEventListener('show.bs.modal', function (e) {
    const btn = e.relatedTarget;
    const id  = btn.getAttribute('data-id');
    const store = btn.getAttribute('data-store');
    document.getElementById('editInventoryId').textContent = id;
    document.getElementById('editStoreId').value = store;
    document.getElementById('editForm').action = '/inventories/' + id;
});
</script>
@endsection
