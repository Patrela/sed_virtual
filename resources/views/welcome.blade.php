<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>SED Inventario MU</title>
    <link rel="stylesheet" href="{{ asset('css/ppal.css') }}">
    <link rel="stylesheet" href="{{ asset('css/product.css') }}">
    <!-- Fonts -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.12.1/css/all.css" crossorigin="anonymous">

    <!-- Styles -->

</head>

<body class="antialiased dark:bg-black dark:text-white/50">
    <div class="bg-gray-50 text-black/50 dark:bg-black dark:text-white/50">
        {{-- <img id="background" class="absolute -left-20 top-0 max-w-[877px]" src="https://laravel.com/assets/img/welcome/background.svg" /> --}}

        <div
            class="relative min-h-screen flex flex-col items-center justify-center selection:bg-[#FF2D20] selection:text-white">
            <div class="relative w-full max-w-2xl px-6 lg:max-w-7xl">
                <header class="mainmenu grid grid-cols-3 items-center gap-2 py-10 lg:grid-cols-3">

                    <div class="flex lg:justify-left lg:col-start-1">
                        <x-application-logo class="h-12 w-auto text-white lg:h-16 lg:text-[#FF2D20]" />
                    </div>

                    @if (Route::has('login'))
                        <nav class="-mx-3 flex flex-1 justify-end">
                            @auth
                                @if (Route::has('profile.edit'))
                                    {{-- <a href="{{ route('profile.edit') }}"
                                        class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white">
                                        Profile
                                    </a> --}}
                                    <a href="{{ route('profile.edit') }}"
                                        class="navitem">
                                        Profile
                                    </a>
                                @endif
                                @if (Route::has('product.index'))
                                    <a href="{{ route('product.index') }}" class="navitem">
                                        Products
                                    </a>
                                @endif
                            @else
                                <a href="{{ route('login') }}" class="navitem">
                                    Log in
                                </a>

                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="navitem">
                                        Register
                                    </a>
                                @endif
                                @if (Route::has('product.index'))
                                    <a href="{{ route('product.index') }}" class="navitem">
                                        Products
                                    </a>
                                @endif
                            @endauth
                        </nav>
                    @endif
                </header>

                <main > <!--class="mt-6" -->
                    <aside>
                        <div class="aside-container" name="main_group"  id="main_group">
                            <h3 class="title">GRUPOS</h3>
                            <button>Button 1</button>
                            <button>Button 2</button>
                            <button>Button 3</button>
                            <button>Más &nbsp;&nbsp;<i class="mx-3 fas fa-caret-down"></i></a></button>
                        </div>
                        <p></p>
                        <div class="aside-container" name="main_group"  id="main_group">
                            <h3 class="title">FILTROS</h3>
                            <button>Button 1</button>
                            <button>Button 2</button>
                            <button>Button 3</button>
                            <button>Más &nbsp;&nbsp;<i class="mx-3 fas fa-caret-down"></i></a></button>
                        </div>
                    </aside>

                    <section class="main-section">
                        <div class="card">
                            <div class="card-title">Card Title 1</div>
                            <div class="card-text">Lorem ipsum dolor sit amet, consectetur adipiscing elit.</div>
                            <img src="https://via.placeholder.com/150" alt="Card Image">
                        </div>
                        <!-- Add more cards here -->
                    </section>
                </main>

                <footer>
                    <p class="footer-top"> Recuerde refrescar la página para un inventario actualizado.</p>
                    <div class="footer-medium">
                        <div class="footer-medium-left">
                            <p class="footer-title">Documentación</p>
                            <p>Documentación técnica para API de Consulta de Inventarios</p>
                        </div>
                        <div class="footer-medium-right">
                            <form action="{{ route('postman.stock') }}" method="POST">
                                @csrf
                                <button type="submit">API Postman</button>
                              </form>

                        </div>
                    </div>
                    <p class="footer-bottom">El contenido de este sitio, incluyendo textos, imágenes y código, es propiedad de SED INTERNATIONAL DE COLOMBIA S.A.S., y está protegido por las leyes internacionales de derecho de autor.</p>
                </footer>
            </div>
        </div>
    </div>
</body>

</html>
