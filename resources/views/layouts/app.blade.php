<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Sistema Clínico') }}</title>

    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Inter:400,500,600,700" rel="stylesheet">

    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    <style>
        body { 
            font-family: 'Inter', sans-serif; 
            background-color: #f8f9fa; /* Fondo gris muy claro para resaltar las tarjetas blancas */
        }
        .navbar-brand { 
            font-weight: 700; 
            letter-spacing: -0.5px; 
        }
        .nav-link { 
            font-weight: 500; 
            transition: color 0.2s ease-in-out; 
            padding-left: 1rem !important;
            padding-right: 1rem !important;
        }
        .nav-link:hover { 
            color: #0d6efd !important; /* Azul Bootstrap al pasar el mouse */
        }
        .dropdown-menu {
            border: none;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            border-radius: 0.5rem;
        }
    </style>
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm border-bottom py-3">
            <div class="container">
                <a class="navbar-brand text-primary d-flex align-items-center gap-2" href="{{ url('/') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-heart-pulse-fill" viewBox="0 0 16 16">
                      <path fill-rule="evenodd" d="M1.475 9C2.702 10.84 4.779 12.871 8 15c3.221-2.129 5.298-4.16 6.525-6H12a.5.5 0 0 1-.464-.314l-1.457-3.642-1.598 5.593a.5.5 0 0 1-.945.049L5.889 6.568l-1.473 2.21A.5.5 0 0 1 4 9z"/>
                      <path fill-rule="evenodd" d="M0.88 8C-2.427 1.68 4.41-2 7.823 1.143q.09.083.176.171a3 3 0 0 1 .176-.17C11.59-2 18.426 1.68 15.12 8h-2.783l-1.874-4.686a.5.5 0 0 0-.936-.044L8.01 8.878 6.517 3.647a.5.5 0 0 0-.926-.076l-2.062 3.093A.5.5 0 0 0 4 7H.88z"/>
                    </svg>
                    {{ config('app.name', 'Laravel') }}
                </a>
                
                <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    
                    <ul class="navbar-nav me-auto ps-md-4">
                        @auth
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('patients.*') ? 'active text-primary fw-bold' : 'text-secondary' }}" 
                                   href="{{ route('patients.index') }}">
                                    Pacientes
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->is('laboratories*') || request()->routeIs('laboratories.*') ? 'active text-primary fw-bold' : 'text-secondary' }}" 
                                href="{{ route('laboratories.index') }}">
                                    Laboratorio
                                </a>
                            </li>
                        @endauth
                    </ul>

                    <ul class="navbar-nav ms-auto">
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif

                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="btn btn-primary px-4 ms-2 rounded-pill" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle fw-bold text-dark d-flex align-items-center gap-2" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    <div class="bg-primary text-white rounded-circle d-flex justify-content-center align-items-center" style="width: 32px; height: 32px; font-size: 14px;">
                                        {{ substr(Auth::user()->name, 0, 1) }}
                                    </div>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end mt-2 p-2" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item rounded text-danger fw-semibold" href="{{ route('logout') }}"
                                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        Cerrar Sesión
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-5">
            @yield('content')
        </main>
    </div>
</body>
</html>