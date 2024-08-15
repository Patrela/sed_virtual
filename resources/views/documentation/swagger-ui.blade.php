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

</head>
<body>
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
