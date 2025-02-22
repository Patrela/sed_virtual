<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>COTIZACION SED</title>
    <style>
        body {
            font-family: Calibri, sans-serif !important;
            font-size: 17px !important;
            margin-left: 48px;
            border-left: 6px solid #FF2D20;
        }

        h2 {
            font-size: 1.5em;
            font-weight: bolder;
            color: darkslateblue;
        }

        detail {
            /* font-family: Calibri, sans-serif;
            font-size: 17px; */
            padding: 10px;
            margin: 5px 5px 20px 5px;
        }

        .card {
            display: flex;
            flex-direction: row;
            gap: 10px;
        }

        .card-image {
            width: 240px; /* 310 */
            margin: 2px;
            padding: 5px;
            border:  none; /* none; 1px solid black; */
            height: 220px; /* 310 */
        }

        .card-image img {
            margin: 0;
            padding: 1px;
            width: 238px; /* 298 */
            height: 218px; /* 298 */
        }
        .card-content {
            margin-left: 10px;
        }

        .main-content {
            margin-left: 3px;
            border-left: 1px solid black;
            padding-left: 15px;
        }
        .title {
            margin-left: 40px;
            margin-top: 60px;
            align-items: center
        }
    </style>

</head>

<body>
    <div class="main-content">
        <div class="card">
            <div class="card-image">
                <img id="img_logo" src="{{ asset('images/mainlogo.png') }}" alt="SED INTERNATIONAL">
            </div>
            <div class="card-content title">
                <h1>COTIZACIÓN SED</h1>
            </div>
        </div>
        <detail>
            <br>
            <p>Fecha: {{ date('Y-m-d') }}</p>
            <p><strong>Contacto: {{ $sender }}</strong></p>
            <p>Agradecemos su confianza e interés por nuestro portafolio. A continuación, adjuntamos la cotización solicitada:</p>
        </detail>
        <div class="card">
            <div class="card-image">
                <img id="prod_img_1" src="{{ $product['image_1'] }}" alt="{{ $product['name'] }}">
            </div>
            <div class="card-content">
                <h2>REF. {{ $product['sku'] }} : {{ $product['name'] }}</h2>
                <h3>STOCK= {{ number_format($product['stock_quantity']) }} {{ $product['unit'] }} MARCA={{ $product['brand'] }}</h3>
                <h3> ${{ number_format($product['regular_price']) }} {{ $product['currency'] }} {{ $product['price_tax_status'] }}</h3>
            </div>
        </div>
        <detail>
            <p>{{ $product['description'] }}</p>
            <p>{{ $product['attributes'] }}. {{ $product['guarantee'] }} </p>
        </detail>

        <p>Condiciones Comerciales:</p>
        <ul>
            <li>Esta cotización es una recomendación, por lo tanto debe ser analizada y aprobada por su departamento de ingeniería.</li>
            <li>Debe revisar los detalles técnicos adjuntos, con el fin de verificar si cumplen lo solicitado por su cliente.</li>
            <li>La disponibilidad y los precios pueden variar sin previo aviso.</li>
            <li>Asegure la mercancía generando la reserva con su ejecutivo de cuenta.</li>
            <li>Los precios no incluyen flete.</li>
            <li>Facturación en pesos a la TRM del día.</li>
            <li>Política de garantías: https://www.sed.international/servicios_info</li>
        </ul>
        <br/>
        <br/>
    </div>
</body>

</html>
