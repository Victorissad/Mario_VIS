@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-12">
            <h1>Modifier l'exemplaire #{{ $inventory['inventoryId'] }}</h1>
            <p class="text-muted">Modification du lieu de stockage uniquement</p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('inventories.update', $inventory['inventoryId']) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label">Film (non modifiable)</label>
                            <input type="text"
                                   class="form-control"
                                   value="@if(isset($inventory['film'])){{ $inventory['film']['title'] }} (@if(isset($inventory['film']['releaseYear'])){{ $inventory['film']['releaseYear'] }}@endif)@else Film ID: {{ $inventory['filmId'] }}@endif"
                                   disabled>
                            <div class="form-text">Le film associé à un exemplaire ne peut pas être modifié.</div>
                        </div>

                        <div class="mb-3">
                            <label for="store_id" class="form-label">Nouveau magasin <span class="text-danger">*</span></label>
                            <select name="store_id" id="store_id" class="form-select @error('store_id') is-invalid @enderror" required>
                                @foreach($stores as $store)
                                    <option value="{{ $store['storeId'] }}"
                                            {{ $inventory['storeId'] == $store['storeId'] ? 'selected' : '' }}>
                                        Magasin #{{ $store['storeId'] }}
                                        @if(isset($store['staffMembers']) && count($store['staffMembers']) > 0)
                                            - {{ $store['staffMembers'][0]['firstName'] }} {{ $store['staffMembers'][0]['lastName'] }}
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('store_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle"></i>
                            <strong>Attention :</strong> Vous pouvez déplacer cet exemplaire même s'il est actuellement en location.
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('inventories.show', $inventory['inventoryId']) }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Annuler
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Enregistrer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
