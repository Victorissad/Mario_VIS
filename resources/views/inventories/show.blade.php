@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-12">
            <h1>Détail de l'exemplaire #{{ $inventory['inventoryId'] }}</h1>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Informations</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tbody>
                            <tr>
                                <th style="width: 200px;">ID de l'exemplaire</th>
                                <td>{{ $inventory['inventoryId'] }}</td>
                            </tr>
                            <tr>
                                <th>Film</th>
                                <td>
                                    @if(isset($inventory['film']))
                                        <strong>{{ $inventory['film']['title'] }}</strong>
                                        @if(isset($inventory['film']['releaseYear']))
                                            ({{ $inventory['film']['releaseYear'] }})
                                        @endif
                                        <br>
                                        <small class="text-muted">Film ID: {{ $inventory['filmId'] }}</small>
                                        @if(isset($inventory['film']['description']))
                                            <br>
                                            <small>{{ $inventory['film']['description'] }}</small>
                                        @endif
                                    @else
                                        Film ID: {{ $inventory['filmId'] }}
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Magasin</th>
                                <td>
                                    Magasin #{{ $inventory['storeId'] }}
                                </td>
                            </tr>
                            <tr>
                                <th>Dernière mise à jour</th>
                                <td>{{ \Carbon\Carbon::parse($inventory['lastUpdate'])->format('d/m/Y à H:i:s') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('inventories.edit', $inventory['inventoryId']) }}" class="btn btn-warning">
                            <i class="bi bi-pencil"></i> Modifier le magasin
                        </a>

                        <form action="{{ route('inventories.destroy', $inventory['inventoryId']) }}"
                              method="POST"
                              onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet exemplaire ?\n\nAttention : La suppression échouera si l\'exemplaire est actuellement en location.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="bi bi-trash"></i> Supprimer l'exemplaire
                            </button>
                        </form>

                        <a href="{{ route('inventories.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Retour à la liste
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
