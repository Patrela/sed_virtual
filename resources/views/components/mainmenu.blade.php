@props(['administrator' => false, 'developer' => false])
<header class="mainmenu">

    <div class="logo">
        <x-application-logo />
    </div>

    <div class="mainmenu-icons">
        <div class="mainmenu-icons-text">
            <x-text-input id="search" type="text" name="search" required autofocus autocomplete="search"/>
        </div>
        <div class="mainmenu-icons-button">
            <form method="GET" action="{{ route('logout') }}" class="mainmenu-icons-text">
                @csrf
                <button class="navitem" type="button"  id="btnSearch" onclick="searchWilcardProduct()">
                    <i class="fas fa-search  navitem-icon"></i>
                </button>
                @auth
                    <span>{{ Auth::user()->name }}</span>
                    @if ($administrator)
                        <button class="navitem" type="button" id="btnClassifications" alt="Load Classifications" onclick="classificationsRoute()">
                            <i class="fas fa-boxes navitem-icon"></i>
                        </button>
                        <button class="navitem" type="button" id="btnUsers"  alt="Load New Users" onclick="usersRoute()"> {{-- onclick="window.location.href = '{{ route('sed.users') }}'" --}}
                            <i class="fas fa-user-friends navitem-icon"></i>
                        </button>
                        <button class="navitem" type="button" id="btnProfiles"  alt="User Profile" onclick="window.location.href = '{{ route('rolesprofile.index') }}'">
                            <i class="fas fa-plus-circle navitem-icon"></i>
                        </button>
                        <button class="navitem" type="button" id="btnAffinity" alt="affinity" onclick="window.location.href = '{{ route('affinity.index') }}'">
                            <i class="fas fa-bell navitem-icon"></i>
                        </button>
                    @endif
                @endauth
                @if ($developer)
                    <button class="navitem" type="button"  id="btnDocumentation" onclick="window.location.href = '{{ route('documentation.show') }}'">
                        <i class="fas fa-atlas navitem-icon"></i>
                    </button>
                @endif
                <button class="navitem" type="button" id="btnStock" onclick="window.location.href = '{{ route('home') }}'">
                    <i class="fas fa-home navitem-icon"></i>
                </button>
                <button class="navitem"  id="btnLogout" type="submit">
                    <i class="fas fa-sign-out-alt navitem-icon"></i>
                </button>
            </form>
        </div>
    </div>
</header>
