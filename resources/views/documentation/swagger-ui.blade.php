<!-- resources/views/swagger-ui.blade.php -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SED API Documentation</title>
    <link rel="icon" href="{{ asset('images/icoSedDigital.png') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('swagger-ui/swagger-ui.css') }}">
    <link rel="stylesheet" href="{{ asset('css/swagger.css') }}">
    <link rel="stylesheet" href="{{ asset('css/stock.css') }}">
    <link rel="stylesheet" href="{{ asset('css/product.css') }}">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.12.1/css/all.css" crossorigin="anonymous">
</head>
<body>
    <header class="mainmenu">
        <div class="logo">
            <x-application-logo />
        </div>
        <div class="mainmenu-icons-large">
            <div class="mainmenu-icons-button">
                <form method="GET" action="{{ route('logout') }}" class="mainmenu-icons-text">
                    @csrf
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
    <div id="swagger-ui"></div>
    <script src="{{ asset('swagger-ui/swagger-ui-bundle.js') }}"></script>
    <script src="{{ asset('swagger-ui/swagger-ui-standalone-preset.js') }}"></script>

    <script>
        window.onload = function() {
            const ui = SwaggerUIBundle({
                url: "{{ $swaggerJsonUrl }}",
                dom_id: '#swagger-ui',
                deepLinking: true,
                presets: [
                    SwaggerUIBundle.presets.apis,
                    SwaggerUIStandalonePreset
                ],
                layout: "StandaloneLayout",
                docExpansion: 'none'
            });
            window.ui = ui;
        };
    </script>
</body>
</html>
