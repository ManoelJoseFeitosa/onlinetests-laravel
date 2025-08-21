<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'OnlineTests') - OnlineTests</title>
    <link rel="icon" href="{{ asset('images/favicon.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
{{-- ## CORREÇÃO 1: Classes adicionadas ao body ## --}}
<body class="bg-light d-flex flex-column min-vh-100">

    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ route('home') }}">
                <i class="bi bi-pencil-square"></i>
                OnlineTests
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#main-nav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="main-nav">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link" href="{{ route('home') }}">Início</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('funcionalidades') }}">Funcionalidades</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('planos') }}">Planos</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('documentos') }}">Documentos</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('contato') }}">Contato</a></li>
                </ul>
                
                <div class="ms-lg-3">
                     <a href="{{ route('login') }}" class="btn btn-primary">Acessar Sistema</a>
                </div>
            </div>
        </div>
    </nav>

    {{-- ## CORREÇÃO 2: Classe adicionada ao main ## --}}
    <main class="flex-grow-1">
        {{ $slot }}
    </main>

    <footer class="bg-dark text-white pt-5 pb-4">
        <div class="container text-center text-md-start">
            <div class="row text-center text-md-start">
                <div class="col-md-3 col-lg-3 col-xl-3 mx-auto mt-3">
                    <h5 class="text-uppercase mb-4 fw-bold text-primary">OnlineTests</h5>
                    <p>Avaliando para evoluir. Nossa missão é otimizar o processo educacional através da tecnologia.</p>
                </div>
                <div class="col-md-2 col-lg-2 col-xl-2 mx-auto mt-3">
                    <h5 class="text-uppercase mb-4 fw-bold">Navegação</h5>
                    <p><a href="{{ route('funcionalidades') }}" class="text-white" style="text-decoration: none;">Funcionalidades</a></p>
                    <p><a href="{{ route('planos') }}" class="text-white" style="text-decoration: none;">Planos</a></p>
                    <p><a href="{{ route('contato') }}" class="text-white" style="text-decoration: none;">Contato</a></p>
                    <p><a href="{{ route('documentos') }}" class="text-white" style="text-decoration: none;">Documentos</a></p>
                    <p><a href="{{ route('politica.privacidade') }}" class="text-white" style="text-decoration: none;">Termos e Privacidade</a></p>
                </div>
                <div class="col-md-4 col-lg-3 col-xl-3 mx-auto mt-3">
                    <h5 class="text-uppercase mb-4 fw-bold">Contato</h5>
                    <p><i class="bi bi-geo-alt-fill me-3"></i>Teresina, PI, Brasil</p>
                    <p><i class="bi bi-envelope-fill me-3"></i>contato@onlinetests.com.br</p>
                    <p>
                        <a href="https://wa.me/5586994533792" class="text-white" style="text-decoration: none;" target="_blank" rel="noopener noreferrer">
                            <i class="bi bi-whatsapp me-3"></i>(86) 99453-3792
                        </a>
                    </p>
                    <p><i class="bi bi-telephone-fill me-3"></i>(86) 99950-3815</p>
                </div>
            </div>
            <hr class="mb-4">
            <div class="row align-items-center">
                <div class="col-md-7 col-lg-8">
                    <p class="text-center text-md-start mb-3 mb-md-0">Copyright © 2025 OnlineTests. Todos os direitos reservados.</p>
                </div>
                <div class="col-md-5 col-lg-4">
                    <div class="text-center text-md-end">
                        <a href="https://www.instagram.com/onlinetests.oficial/" class="text-white" style="text-decoration: none;" target="_blank" rel="noopener noreferrer">
                            <i class="bi bi-instagram me-2"></i>onlinetests.oficial
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>