<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>@yield('title')</title>
        <!-- Fonte -->
        <link href="https://fonts.googleapis.com/css2?family=Roboto" rel="stylesheet">
        <!-- Bootstrap -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
        <!-- CSS -->
        <link rel="stylesheet" href="/css/styles.css">
        <script src="/js/script.js"></script>
    </head>
    <body>
        <header>
            <nav class="navbar navbar-expand-sm navbar-light">
                <a href="/" class="navbar-brand">
                    <img src="/img/logo.webp" alt="logo">
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar" aria-controls="navbar" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbar">
                    <ul class="navbar-nav">
                        @if (Auth::check())
                            <li class="nav-item">
                                <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="nav-link btn">Logout</button>
                                </form>
                            </li>
                        @else
                            <li class="nav-item">
                                <a href="{{ route('login') }}" class="nav-link">Entrar</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('register') }}" class="nav-link">Registrar</a>
                            </li>
                        @endif
                        @if (Auth::check() && Auth::user()->role === 'admin')
                            <li class="nav-item">
                                <a href="{{ route('admin.index') }}" class="nav-link">Painel Admin</a>
                            </li>
                        @endif
                    </ul>
                </div>
            </nav>
        </header>
        @yield('content')
        <footer>
            <p>SITEZUDO LEGAL &copy; 2025</p>
        </footer>
        <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
        <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    </body>
</html>
