@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-12">
            <h1>Magasin #{{ $store['storeId'] }}</h1>
            @if(isset($store['staffMembers']) && count($store['staffMembers']) > 0)
                <p class="text-muted">
                    Responsable : {{ $store['staffMembers'][0]['firstName'] }} {{ $store['staffMembers'][0]['lastName'] }}
                </p>
            @endif
        </div>
    </div>

    {{-- Statistiques du magasin --}}
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <h2 class="display-4">{{ $statistics['total'] }}</h2>
                    <p class="text-muted">Exemplaires totaux</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <h2 class="display-4 text-success">{{ $statistics['available'] }}</h2>
                    <p class="text-muted">Disponibles</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <h2 class="display-4 text-warning">{{ $statistics['rented'] }}</h2>
                    <p class="text-muted">En location</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-12">
            <h3>Inventaire du magasin</h3>
        </div>
    </div>

    {{-- Liste des exemplaires du magasin --}}
    @if(count($inventories) > 0)
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Film</th>
                                <th>Dernière MAJ</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($inventories as $inventory)
                                <tr>
                                    <td>{{ $inventory['inventoryId'] }}</td>
                                    <td>
                                        @if(isset($inventory['film']))
                                            <strong>{{ $inventory['film']['title'] }}</strong>
                                            @if(isset($inventory['film']['releaseYear']))
                                                <span class="text-muted">({{ $inventory['film']['releaseYear'] }})</span>
                                            @endif
                                        @else
                                            Film ID: {{ $inventory['filmId'] }}
                                        @endif
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($inventory['lastUpdate'])->format('d/m/Y H:i') }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('inventories.show', $inventory['inventoryId']) }}"
                                           class="btn btn-sm btn-info"
                                           title="Voir">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('inventories.edit', $inventory['inventoryId']) }}"
                                           class="btn btn-sm btn-warning"
                                           title="Modifier">
                                            <i class="bi bi-pencil"></i>
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
        <div class="alert alert-info">
            <i class="bi bi-info-circle"></i> Ce magasin ne contient aucun exemplaire.
        </div>
    @endif

    <div class="row mt-4">
        <div class="col-md-12">
            <a href="{{ route('stores.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Retour à la liste des magasins
            </a>
            <a href="{{ route('inventories.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-list"></i> Voir tout l'inventaire
            </a>
        </div>
    </div>
</div>
@endsection
