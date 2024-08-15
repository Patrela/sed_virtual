<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SED Stock</title>
    <link rel="icon" href="{{ asset('images/icoSedDigital.png') }}">
    <link rel="stylesheet" href="{{ asset('css/stock.css') }}">
    <link rel="stylesheet" href="{{ asset('css/product.css') }}">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.12.1/css/all.css" crossorigin="anonymous">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    {{-- <script type="text/javascript">
        let allProducts = @json($products); // Global variable to store all products
    </script> --}}
</head>

<body>
    <header class="mainmenu">

        <div class="logo">
            <x-application-logo />
        </div>
        <div class="mainmenu-icons">
            <div class="mainmenu-icons-text">
                {{-- <x-text-input id="search" type="text" name="search" required autofocus autocomplete="search"/> --}}
                <x-text-input id="searchUser" type="text" name="searchUser" required autofocus autocomplete="search"
                    placeholder="Search Users by Name" />
            </div>
            <div class="mainmenu-icons-button">
                <form method="GET" action="{{ route('logout') }}" class="mainmenu-icons-text">
                    @csrf
                    @auth
                        <span>{{ Auth::user()->name }}</span>
                        {{-- @if ($administrator) --}}
                        <button class="navitem" type="button" onclick="searchUsers()">
                            <i class="fas fa-search  navitem-icon"></i>
                        </button>
                        {{-- @endif --}}
                    @endauth
                </form>
            </div>
        </div>
    </header>
    <main>
        <h2 class="text-2xl font-semibold mb-4">User Role Assignment</h2>
        <div id="main-container"></div>

    </main>



</body>
<script type="text/javascript">
        function temporalIndicator() {
        const cardsContainer = document.getElementById("main-container");
        cardsContainer.innerHTML = "";

        const temporal = document.createElement("div");
        temporal.id = "temporal";
        temporal.classList.add("temporal");

        const temporal_title = document.createElement("label");
        temporal_title.textContent = "En Proceso ...";

        const temporal_progress = document.createElement("progress");
        temporal_progress.max = 100;
        temporal_progress.value = 70;
        temporal.appendChild(temporal_title);
        temporal.appendChild(temporal_progress);
        cardsContainer.appendChild(temporal);
    }
    function searchUsers() {
                let searchText = document.getElementById('searchUser').value;
                searchText = searchText.trim();
                if (searchText.length > 0) {
                    newpath = "{{ route('isolatedprofile.mail', ['email' => ':email']) }}".replace(':email', encodeURIComponent(searchText));
                    alert(newpath);
                    console.log('path =' + newpath);
                    if (searchText !== '') {
                        //window.location.href = "{{ route('isolatedprofile.mail', ['email' => ':email']) }}".replace(':email', encodeURIComponent(searchText));
                        axios.get( `${newpath}`)
                            .then(response => {
                                this.searchResults = response.data;
                            })
                            .catch(error => {
                                console.error('Error searching users:', error);
                            });
                    } else {
                        this.searchResults = [];
                    }
                    }

                }
    }
</script>

</html>
