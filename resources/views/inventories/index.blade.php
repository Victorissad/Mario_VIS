{{--
    Vue: Liste de tous les exemplaires de l'inventaire
    Route: GET /inventories

    Affiche un tableau avec tous les DVD de tous les magasins
--}}

@extends('layouts.app')

@section('content')
<div class="container">
    {{-- En-tête de la page --}}
    <div class="row mb-4">
        <div class="col-md-12">
            <h1>Gestion de l'Inventaire</h1>
            <p class="text-muted">Liste de tous les exemplaires de DVD</p>
        </div>
    </div>

    {{-- Boutons d'action principaux --}}
    <div class="row mb-3">
        <div class="col-md-12">
            <a href="{{ route('inventories.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Ajouter des exemplaires
            </a>
            <a href="{{ route('stores.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-shop"></i> Voir les magasins
            </a>
        </div>
    </div>

    {{-- Tableau des exemplaires --}}
    @if(count($inventories) > 0)
        <div class="card">
            <div class="card-body">
                <p class="mb-3"><strong>Total : {{ count($inventories) }} exemplaires</strong></p>

                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Film</th>
                                <th>Magasin</th>
                                <th>Dernière MAJ</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Boucle sur tous les exemplaires --}}
                            @foreach($inventories as $inventory)
                                <tr>
                                    <td>{{ $inventory['inventoryId'] }}</td>
                                    <td>
                                        {{-- Affichage du titre du film --}}
                                        @if(isset($inventory['film']))
                                            <strong>{{ $inventory['film']['title'] }}</strong>
                                            @if(isset($inventory['film']['releaseYear']))
                                                <span class="text-muted">({{ $inventory['film']['releaseYear'] }})</span>
                                            @endif
                                        @else
                                            Film ID: {{ $inventory['filmId'] }}
                                        @endif
                                    </td>
                                    <td>
                                        {{-- Lien vers le détail du magasin --}}
                                        <a href="{{ route('stores.show', $inventory['storeId']) }}">
                                            Magasin #{{ $inventory['storeId'] }}
                                        </a>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($inventory['lastUpdate'])->format('d/m/Y H:i') }}</td>
                                    <td class="text-end">
                                        {{-- Boutons d'action --}}
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
                                        {{-- Formulaire de suppression avec confirmation --}}
                                        <form action="{{ route('inventories.destroy', $inventory['inventoryId']) }}"
                                              method="POST"
                                              style="display:inline;"
                                              onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet exemplaire ?');">
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
                </div>
            </div>
        </div>
    @else
        {{-- Message si aucun exemplaire --}}
        <div class="alert alert-info">
            <i class="bi bi-info-circle"></i> Aucun exemplaire trouvé dans l'inventaire.
        </div>
    @endif
</div>
@endsection
