@extends('layouts.dvd-layout')

@section('title', 'Liste des films - DVD Rental')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h1 class="display-4">
            <i class="bi bi-film text-primary"></i> Liste des films
        </h1>
        @if(isset($films) && count($films) > 0)
            <p class="text-muted">Total : <strong>{{ count($films) }}</strong> films disponibles</p>
        @endif
    </div>
</div>

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
                            <th style="width: 100px;">Tarif</th>
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
                                    @if(isset($film['rentalRate']))
                                        <span class="text-success fw-bold">{{ number_format($film['rentalRate'], 2) }} €</span>
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
                                    <a href="/films/{{ $film['filmId'] }}" class="btn btn-sm btn-primary">
                                        <i class="bi bi-eye"></i> Détails
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
    <div class="alert alert-warning" role="alert">
        <i class="bi bi-exclamation-triangle"></i> Aucun film trouvé.
    </div>
@endif
@endsection
