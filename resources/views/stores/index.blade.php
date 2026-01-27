@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-12">
            <h1>Liste des magasins</h1>
            <p class="text-muted">Tous les magasins du réseau</p>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-12">
            <a href="{{ route('inventories.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Retour à l'inventaire
            </a>
        </div>
    </div>

    @if(count($stores) > 0)
        <div class="row">
            @foreach($stores as $store)
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Magasin #{{ $store['storeId'] }}</h5>
                        </div>
                        <div class="card-body">
                            @if(isset($store['staffMembers']) && count($store['staffMembers']) > 0)
                                <p class="mb-2">
                                    <strong>Responsable :</strong><br>
                                    {{ $store['staffMembers'][0]['firstName'] }} {{ $store['staffMembers'][0]['lastName'] }}
                                </p>
                            @endif

                            <p class="mb-3">
                                <strong>Stock total :</strong>
                                <span class="badge bg-primary">{{ $store['inventoryCount'] ?? 0 }} exemplaires</span>
                            </p>

                            <a href="{{ route('stores.show', $store['storeId']) }}" class="btn btn-primary">
                                <i class="bi bi-eye"></i> Voir le détail
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="alert alert-info">
            <i class="bi bi-info-circle"></i> Aucun magasin trouvé.
        </div>
    @endif
</div>
@endsection
