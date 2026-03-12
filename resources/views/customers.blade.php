@extends('layouts.dvd-layout')

@section('title', 'Gestion des clients - RFTG')

@section('content')
<div class="row mb-4 align-items-center">
    <div class="col">
        <h1 class="display-4">
            <i class="bi bi-people text-primary"></i> Gestion des clients
        </h1>
        @if(isset($customers) && count($customers) > 0)
            <p class="text-muted">Total : <strong>{{ count($customers) }}</strong> clients</p>
        @endif
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="bi bi-check-circle"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger">
        @foreach($errors->all() as $error) <p class="mb-0">{{ $error }}</p> @endforeach
    </div>
@endif

@if(isset($customers) && count($customers) > 0)
    <div class="card shadow">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th style="width:60px;">ID</th>
                            <th>Nom</th>
                            <th>Prénom</th>
                            <th>Email</th>
                            <th style="width:100px;">Magasin</th>
                            <th style="width:90px;">Statut</th>
                            <th style="width:120px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($customers as $customer)
                            <tr>
                                <td class="text-muted">#{{ $customer['customerId'] ?? '-' }}</td>
                                <td><strong>{{ $customer['lastName'] ?? '-' }}</strong></td>
                                <td>{{ $customer['firstName'] ?? '-' }}</td>
                                <td><small>{{ $customer['email'] ?? '-' }}</small></td>
                                <td>
                                    <span class="badge bg-secondary">
                                        <i class="bi bi-shop"></i> #{{ $customer['storeId'] ?? '-' }}
                                    </span>
                                </td>
                                <td>
                                    @if(isset($customer['active']) && $customer['active'])
                                        <span class="badge bg-success">Actif</span>
                                    @else
                                        <span class="badge bg-danger">Inactif</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="/customers/{{ $customer['customerId'] }}/edit" class="btn btn-sm btn-warning">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form method="POST" action="/customers/{{ $customer['customerId'] }}"
                                              onsubmit="return confirm('Supprimer le client {{ addslashes(($customer['firstName'] ?? '') . ' ' . ($customer['lastName'] ?? '')) }} ?')">
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
    <div class="alert alert-warning">
        <i class="bi bi-exclamation-triangle"></i> Aucun client trouvé.
    </div>
@endif
@endsection
