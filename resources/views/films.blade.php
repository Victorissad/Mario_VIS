@extends('layouts.dvd-layout')

@section('title', 'Liste des films - RFTG')

@section('content')
<div class="row mb-4 align-items-center">
    <div class="col">
        <h1 class="display-4">
            <i class="bi bi-film text-primary"></i> Liste des films
        </h1>
        <p class="text-muted">Total : <strong id="totalFilms">-</strong> films disponibles</p>
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

<div class="card shadow">
    <div class="card-body p-0">

        <div class="d-flex justify-content-end align-items-center p-3 border-bottom">
            <label for="limit" class="mb-0 me-2">Afficher :</label>
            <select id="limit" class="form-select form-select-sm" style="width: auto;">
                @foreach ($allowedLimits as $l)
                    <option value="{{ $l }}">{{ $l }}</option>
                @endforeach
            </select>
            <span class="text-muted ms-2">par page</span>
        </div>

        <div id="loading" class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Chargement...</span>
            </div>
        </div>

        <div id="noFilms" class="alert alert-warning m-3" style="display: none;">
            <i class="bi bi-exclamation-triangle"></i> Aucun film trouvé.
        </div>

        <div id="filmsContainer" style="display: none;">
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
                    <tbody id="filmsTableBody"></tbody>
                </table>
            </div>

            <div id="paginationContainer" class="p-3 d-flex justify-content-between align-items-center border-top">
                <p class="text-muted mb-0">
                    Page <strong id="currentPageInfo">1</strong> sur <strong id="totalPagesInfo">1</strong>
                </p>
                <nav>
                    <ul class="pagination mb-0" id="paginationList"></ul>
                </nav>
            </div>
        </div>

    </div>
</div>

<script>
const FilmsPagination = {
    currentPage: 1,
    limit: 10,
    totalPages: 1,
    csrfToken: '{{ csrf_token() }}',
    dataUrl: '{{ route("films.data") }}',

    init() {
        this.loadFilms();
        document.getElementById('limit').addEventListener('change', (e) => {
            this.limit = parseInt(e.target.value);
            this.currentPage = 1;
            this.loadFilms();
        });
    },

    async loadFilms() {
        document.getElementById('loading').style.display = 'block';
        document.getElementById('filmsContainer').style.display = 'none';
        document.getElementById('noFilms').style.display = 'none';

        try {
            const response = await fetch(this.dataUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ page: this.currentPage, limit: this.limit })
            });

            const data = await response.json();
            document.getElementById('loading').style.display = 'none';

            if (!data.films || data.films.length === 0) {
                document.getElementById('noFilms').style.display = 'block';
                return;
            }

            this.totalPages = data.totalPages;
            this.renderFilms(data.films);
            this.renderPagination(data);
            document.getElementById('filmsContainer').style.display = 'block';

        } catch (error) {
            document.getElementById('loading').style.display = 'none';
            document.getElementById('noFilms').style.display = 'block';
        }
    },

    renderFilms(films) {
        const tbody = document.getElementById('filmsTableBody');
        tbody.innerHTML = '';

        const ratingColors = { 'G': 'success', 'PG': 'info', 'PG-13': 'warning', 'R': 'danger', 'NC-17': 'dark' };

        films.forEach(film => {
            const filmId   = film.filmId ?? '-';
            const title    = film.title ?? 'Sans titre';
            const desc     = film.description ?? '-';
            const descShort = desc.length > 100 ? desc.substring(0, 100) + '...' : desc;
            const year     = film.releaseYear ?? '-';
            const length   = film.length ? film.length + ' min' : '-';
            const rating   = film.rating ?? null;
            const color    = ratingColors[rating] ?? 'secondary';
            const ratingBadge = rating ? `<span class="badge bg-${color}">${rating}</span>` : '-';

            const row = document.createElement('tr');
            row.innerHTML = `
                <td class="text-muted">#${filmId}</td>
                <td><strong>${this.escapeHtml(title)}</strong></td>
                <td><small class="text-muted">${this.escapeHtml(descShort)}</small></td>
                <td><span class="badge bg-secondary">${year}</span></td>
                <td><i class="bi bi-clock"></i> ${length}</td>
                <td>${ratingBadge}</td>
                <td>
                    <div class="d-flex gap-1">
                        <a href="/films/${filmId}" class="btn btn-sm btn-primary"><i class="bi bi-eye"></i></a>
                        <a href="/films/${filmId}/edit" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></a>
                        <form method="POST" action="/films/${filmId}" onsubmit="return confirm('Supprimer ce film ?')">
                            <input type="hidden" name="_token" value="${this.csrfToken}">
                            <input type="hidden" name="_method" value="DELETE">
                            <button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </div>
                </td>
            `;
            tbody.appendChild(row);
        });
    },

    renderPagination(data) {
        document.getElementById('totalFilms').textContent = data.totalFilms;
        document.getElementById('currentPageInfo').textContent = data.currentPage;
        document.getElementById('totalPagesInfo').textContent = data.totalPages;

        const list = document.getElementById('paginationList');
        list.innerHTML = '';

        if (data.totalPages <= 1) {
            document.getElementById('paginationContainer').style.display = 'none';
            return;
        }
        document.getElementById('paginationContainer').style.display = 'flex';

        list.appendChild(this.createPageItem('«', 1, data.currentPage === 1));
        list.appendChild(this.createPageItem('‹', Math.max(1, data.currentPage - 1), data.currentPage === 1));

        const start = Math.max(1, data.currentPage - 2);
        const end   = Math.min(data.totalPages, data.currentPage + 2);

        if (start > 1) {
            list.appendChild(this.createPageItem('1', 1, false));
            if (start > 2) list.appendChild(this.createPageItem('...', null, true));
        }
        for (let i = start; i <= end; i++) {
            list.appendChild(this.createPageItem(i.toString(), i, false, i === data.currentPage));
        }
        if (end < data.totalPages) {
            if (end < data.totalPages - 1) list.appendChild(this.createPageItem('...', null, true));
            list.appendChild(this.createPageItem(data.totalPages.toString(), data.totalPages, false));
        }

        list.appendChild(this.createPageItem('›', Math.min(data.totalPages, data.currentPage + 1), data.currentPage === data.totalPages));
        list.appendChild(this.createPageItem('»', data.totalPages, data.currentPage === data.totalPages));
    },

    createPageItem(text, page, disabled, active = false) {
        const li = document.createElement('li');
        li.className = 'page-item' + (disabled ? ' disabled' : '') + (active ? ' active' : '');

        if (disabled || page === null) {
            li.innerHTML = `<span class="page-link">${text}</span>`;
        } else {
            const a = document.createElement('a');
            a.className = 'page-link';
            a.href = '#';
            a.innerHTML = text;
            a.addEventListener('click', (e) => {
                e.preventDefault();
                this.currentPage = page;
                this.loadFilms();
            });
            li.appendChild(a);
        }
        return li;
    },

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
};

document.addEventListener('DOMContentLoaded', () => FilmsPagination.init());
</script>
@endsection
