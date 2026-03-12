@extends('layouts.dvd-layout')

@section('title', 'Liste des films - RFTG')

@section('content')
<div class="row mb-4 align-items-center">
    <div class="col">
        <h1 class="display-4">
            <i class="bi bi-film text-primary"></i> Liste des films
        </h1>
        @if(isset($films) && count($films) > 0)
            <p class="text-muted">Total : <strong>{{ count($films) }}</strong> films disponibles</p>
        @endif
    </div>
    <div class="col-auto">
        <a href="/films/create" class="btn btn-success">
            <i class="bi bi-plus-circle"></i> Ajouter un film
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="bi bi-check-circle"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(isset($films) && count($films) > 0)
    <div class="card shadow">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th style="width: 50px;">ID</th>
                            <th>Titre</th>
                            <th style="width: 300px;">Description</th>
                            <th style="width: 80px;">Année</th>
                            <th style="width: 100px;">Durée</th>
                            <th style="width: 120px;">Classement</th>
                            <th style="width: 120px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($films as $film)
                            <tr>
                                <td class="text-muted">#{{ $film['filmId'] ?? '-' }}</td>
                                <td>
                                    <strong>{{ $film['title'] ?? 'Sans titre' }}</strong>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        {{ isset($film['description']) ? (strlen($film['description']) > 100 ? substr($film['description'], 0, 100) . '...' : $film['description']) : '-' }}
                                    </small>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $film['releaseYear'] ?? '-' }}</span>
                                </td>
                                <td>
                                    @if(isset($film['length']))
                                        <i class="bi bi-clock"></i> {{ $film['length'] }} min
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if(isset($film['rating']))
                                        @php
                                            $badgeColor = match($film['rating']) {
                                                'G' => 'success',
                                                'PG' => 'info',
                                                'PG-13' => 'warning',
                                                'R' => 'danger',
                                                'NC-17' => 'dark',
                                                default => 'secondary'
                                            };
                                        @endphp
                                        <span class="badge bg-{{ $badgeColor }}">{{ $film['rating'] }}</span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="/films/{{ $film['filmId'] }}" class="btn btn-sm btn-primary">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="/films/{{ $film['filmId'] }}/edit" class="btn btn-sm btn-warning">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form method="POST" action="/films/{{ $film['filmId'] }}"
                                              onsubmit="return confirm('Supprimer ce film ?')">
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
        <i class="bi bi-exclamation-triangle"></i> Aucun film trouvé.
    </div>
@endif
@endsection
