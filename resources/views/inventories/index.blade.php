{{--
    Vue: Inventaire groupé par film et magasin
    Route: GET /inventories
--}}

@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Gestion de l'Inventaire</h5>
                    <a href="{{ route('inventories.create') }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-plus-circle"></i> Ajouter des exemplaires
                    </a>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    @if(empty($grouped))
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> Aucun exemplaire trouvé dans l'inventaire.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Titre du film</th>
                                        <th>Magasin</th>
                                        <th>Exemplaires</th>
                                        <th>Note</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($grouped as $row)
                                        <tr>
                                            <td><strong>{{ $row['title'] }}</strong></td>
                                            <td><i class="bi bi-shop"></i> Magasin #{{ $row['storeId'] }}</td>
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
                                            <td class="text-end">
                                                <a href="{{ route('inventories.create') }}?film_id={{ $row['filmId'] }}&store_id={{ $row['storeId'] }}"
                                                   class="btn btn-sm btn-outline-primary" title="Ajouter des exemplaires">
                                                    <i class="bi bi-plus-circle"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <p class="text-muted mt-2">
                            <i class="bi bi-info-circle"></i>
                            <strong>{{ count($grouped) }}</strong> ligne(s) &mdash;
                            <strong>{{ $totalItems }}</strong> exemplaire(s) au total
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
