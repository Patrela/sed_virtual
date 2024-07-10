@props(['administrator' => false])
<header class="mainmenu">

    <div class="logo">
        <x-application-logo />
    </div>
    {{--
    @if (Route::has('login'))
        <nav class="-mx-3 flex flex-1 justify-end">
            @auth
                @if (Route::has('profile.edit'))
                    <a href="{{ route('profile.edit') }}" class="navitem">
                        Perfil
                    </a>
                    <a href="{{ route('profile.abilities') }}" onclick="getAbilities()" class="navitem">
                        Permisos
                    </a>
                    <a href="{{ route('sed.getProviderGroups') }}" class="navitem">
                        Importar Grupos
                    </a>
                @endif
                @if (Route::has('stock'))
                    <a href="{{ route('stock') }}" class="navitem">
                        Productos
                    </a>
                @endif
            @else
                <a href="{{ route('login') }}" class="navitem">
                    Log in
                </a>

                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="navitem">
                        Registro
                    </a>
                @endif
                @if (Route::has('stock'))
                    <a href="{{ route('stock') }}" class="navitem">
                        Productos
                    </a>
                @endif
            @endauth
        </nav>
    @endif
    --}}
    <div class="mainmenu-icons">
        <div class="mainmenu-icons-text">
            <x-text-input id="search" type="text" name="search" required autofocus autocomplete="search"/>
        </div>
        <div class="mainmenu-icons-button">
            {{-- <a href="{{ route('product.search') }}" onclick="searchProduct()"><i
                    class="fas fa-search  navitem-icon"></i></a>
            <a href="{{ route('home') }}"><i class="fas fa-home navitem-icon"></i></a> --}}
            {{-- <form id="homeForm" method="get" action="{{ route('stock') }}" class="mainmenu-icons-text"> --}}
            <form method="GET" action="{{ route('logout') }}" class="mainmenu-icons-text">
                    @csrf
                @csrf
                <button class="navitem" type="button" onclick="searchWilcardProduct()">
                    <i class="fas fa-search  navitem-icon"></i>
                </button>
                @auth
                    <span>{{ Auth::user()->name }}</span>
                    @if ($administrator)
                        @if (Route::has('register'))
                            <button class="navitem" type="button"  alt="Create User" onclick="window.location.href = '{{ route('register') }}'">
                                <i class="fas fa-plus-circle navitem-icon"></i>
                            </button>
                        @endif
                        <button class="navitem" type="button"  alt="Load New Users" onclick="classificationsRoute()">
                            <i class="fas fa-boxes navitem-icon"></i>
                        </button>
                        <button class="navitem" type="button"  alt="Load New Users" onclick="usersRoute()"> {{-- onclick="window.location.href = '{{ route('sed.users') }}'" --}}
                            <i class="fas fa-user-friends navitem-icon"></i>
                        </button>
                    @endif
                @endauth
                <button class="navitem" type="button"  onclick="window.location.href = '{{ route('stock') }}'">
                    <i class="fas fa-home navitem-icon"></i>
                </button>
                <button class="navitem" type="submit">
                    <i class="fas fa-sign-out-alt navitem-icon"></i>
                </button>
            </form>
        </div>
    </div>
</header>
