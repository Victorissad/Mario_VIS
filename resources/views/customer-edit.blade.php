@extends('layouts.dvd-layout')

@section('title', 'Modifier le client - RFTG')

@section('content')
<div class="mb-3">
    <a href="/customers" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Retour aux clients
    </a>
</div>

<div class="card shadow-lg" style="max-width: 600px; margin: auto;">
    <div class="card-header bg-warning text-white">
        <h2 class="mb-0">
            <i class="bi bi-pencil"></i>
            Modifier : {{ $customer['firstName'] ?? '' }} {{ $customer['lastName'] ?? '' }}
        </h2>
    </div>
    <div class="card-body">

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="/customers/{{ $customer['customerId'] }}">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Prénom <span class="text-danger">*</span></label>
                    <input type="text" name="firstName" class="form-control @error('firstName') is-invalid @enderror"
                           value="{{ old('firstName', $customer['firstName'] ?? '') }}" required>
                    @error('firstName') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Nom <span class="text-danger">*</span></label>
                    <input type="text" name="lastName" class="form-control @error('lastName') is-invalid @enderror"
                           value="{{ old('lastName', $customer['lastName'] ?? '') }}" required>
                    @error('lastName') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Email <span class="text-danger">*</span></label>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email', $customer['email'] ?? '') }}" required>
                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="active" id="active"
                           {{ (isset($customer['active']) && $customer['active']) ? 'checked' : '' }}>
                    <label class="form-check-label fw-bold" for="active">Client actif</label>
                </div>
            </div>

            <div class="d-flex gap-2 mt-3">
                <button type="submit" class="btn btn-warning">
                    <i class="bi bi-check-circle"></i> Enregistrer
                </button>
                <a href="/customers" class="btn btn-secondary">Annuler</a>
            </div>
        </form>
    </div>
</div>
@endsection
