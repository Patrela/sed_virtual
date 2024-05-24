<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <link rel="stylesheet" href="{{ asset('css/ppal.css') }}">
    <link rel="stylesheet" href="{{ asset('css/product.css') }}">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.12.1/css/all.css" crossorigin="anonymous">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <section class="error-container">
        <span><span>4</span></span>
        <span>1</span>
        <span><span>9</span></span>
    </section>

    <footer>
        <p class="footer-top"> No encontramos la página.</p>
        <div class="footer-medium">
            <div class="footer-medium-left">
                <p class="footer-title">Volver al inicio</p>
            </div>
            <div class="footer-medium-right">
                <form action="{{ route('logout') }}" method="GET">
                    @csrf
                    <button type="submit">Inicio</button>
                </form>
            </div>
        </div>
        <p class="footer-bottom">Este sitio es propiedad de SED INTERNATIONAL DE COLOMBIA S.A.S., y está protegido por
            las leyes internacionales de derecho de autor.</p>
    </footer>

</body>

</html>
