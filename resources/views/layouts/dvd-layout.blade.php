<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin - RFTG')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        .navbar-nav .nav-link {
            position: relative;
            transition: all 0.3s ease;
        }
        .navbar-nav .nav-link:hover {
            transform: translateY(-2px);
        }
        .navbar-nav .nav-link.active::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80%;
            height: 3px;
            background: #fff;
            border-radius: 2px;
        }
        .page-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body class="bg-light">
    <!-- Navbar responsive -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top shadow">
        <div class="container-fluid">
            <a class="navbar-brand" href="/">
                <i class="bi bi-film"></i> Admin - RFTG
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('films*') ? 'active' : '' }}" href="/films">
                            <i class="bi bi-film"></i> Films
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('inventories*') ? 'active' : '' }}" href="/inventories">
                            <i class="bi bi-box"></i> Inventaire
                        </a>
                    </li>
<li class="nav-item ms-2">
                        <form method="POST" action="/logout" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-light btn-sm">
                                <i class="bi bi-box-arrow-right"></i> Déconnexion
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Contenu principal -->
    <div class="container-fluid py-4">
        @yield('content')
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-3 mt-5">
        <div class="container">
            <small>
                <i class="bi bi-database"></i>Admin - RFTG
                <span class="mx-2">|</span>
                <i class="bi bi-code-slash"></i> Laravel + Spring Boot
            </small>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
