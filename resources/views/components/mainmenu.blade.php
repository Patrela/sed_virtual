@props(['administrator' => false, 'developer' => false, 'rolevalue' => false, 'ordermanager' => false, 'profile_list' => false])
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
                    <!-- <span>Role  {{ $rolevalue }}</span>                     
                    @if (isset($rolevalue))
                        <span>Role Value: {{ $rolevalue }}</span>
                        @if (isset($profile_list) && isset($profile_list[$rolevalue]))
                            <i class="fas fa-anchor navitem-icon"></i><span>Profile List: {{ $profile_list[$rolevalue] }}</span>
                        @endif
                    @endif                               $administrator      -->
                    @if ($profile_list[$rolevalue] == "Administrator")
                        <button class="navitem" type="button" id="btnClassifications" alt="Load Classifications" onclick="classificationsRoute()">
                            <i class="fas fa-boxes navitem-icon"></i>
                        </button>
                        <button class="navitem" type="button" id="btnUsers"  alt="Load New Users" onclick="usersRoute()"> {{-- onclick="window.location.href = '{{ route('epicor.users') }}'" --}}
                            <i class="fas fa-user-friends navitem-icon"></i>
                        </button>
                        <button class="navitem" type="button" id="btnProfiles"  alt="User Profile" onclick="window.location.href = '{{ route('rolesprofile.index') }}'">
                            <i class="fas fa-plus-circle navitem-icon"></i>
                        </button>
                        <button class="navitem" type="button" id="btnAffinity" alt="affinity" onclick="window.location.href = '{{ route('affinity.index') }}'">
                            <i class="fas fa-bell navitem-icon"></i>
                        </button>
                        <button class="navitem" type="button" id="btnVisits"  alt="Visitors" onclick="window.location.href = '{{ route('visits.index') }}'">
                            <i class="fas fa-globe navitem-icon"></i>
                        </button>                       
                        <button class="navitem" type="button" id="btnBrokenAnchors"  alt="Broken Anchors" onclick="urlImagesRoute()"> {{-- onclick="window.location.href = '{{ route('file.getWrongUrlImageProducts') }}'"> --}}
                            <i class="fas fa-anchor navitem-icon"></i>
                        </button>
                    @endif

                    @if ($profile_list[$rolevalue] == "Administrator" || $profile_list[$rolevalue] == "Developer")
                        {{-- <button class="navitem" type="button"  id="btnDocumentation" onclick="window.location.href = '{{ route('documentation.show') }}'"> --}}
                            <button class="navitem" type="button"  id="btnDocumentation" onclick="window.location.href = '{{  route('documentation.dynamic',['source' => '1']) }}'">
                            <i class="fas fa-atlas navitem-icon"></i>
                        </button>
                        <button class="navitem" type="button"  id="btnDocumentationLocal" onclick="window.location.href = '{{ route('documentation.dynamic',['source' => '2']) }}'">
                            <i class="fas fa-book-open navitem-icon"></i>
                        </button>

                    @endif
                              
                @if ($profile_list[$rolevalue] == "Administrator" || $profile_list[$rolevalue] == "OrderManager")
                    <button class="navitem" type="button" id="btnOrders"  alt="Orders" onclick="window.location.href = '{{ route('order.index') }}'">
                        <i class="fas fa-check-double navitem-icon"></i>
                    </button>   
                @endif                    
                  
                @endauth

    

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
<script src="https://unpkg.com/@popperjs/core@2"></script>
<script src="https://unpkg.com/tippy.js@6"></script>
<script>
    // tooltips for buttons
    tippy('#btnSearch', {
        content: 'Search Product by SKU / brand + group / special characteristic',
    });    
    tippy('#btnClassifications', {
        content: 'Clear Memory and Updating Groups-Brands-Categories from Epicor',
    });
    tippy('#btnUsers', {
        content: 'Updating Staff and Trades',
    });
    tippy('#btnProfiles', {
        content: 'Updating Users Profile',
    });
    tippy('#btnAffinity', {
        content: 'Record the Brand Affinities Programs',
    });
    tippy('#btnVisits', {
        content: 'Visitors by Trades',
    });  
    tippy('#btnBrokenAnchors', {
        content: 'Broken Anchors',
    });  
    tippy('#btnDocumentation', {
        content: 'API Documentation for retrieving SED Stock',
    });
    tippy('#btnDocumentationLocal', {
        content: 'API Documentation for sales',
    });
    tippy('#btnOrders', { 
        content: 'Orders List',
    });
    tippy('#btnStock', {
        content: 'Stock',
    });
    tippy('#btnLogout', {
        content: 'Exit program and user',
    });   
</script>
