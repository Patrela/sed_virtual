<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ONLINE ORDER SED</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
        }

        .item {
            flex: 1; 
            /* display: flex;
            flex-direction: column;
            padding: 5px;
            margin: 0px;
            border: 1px solid #ddd;
            box-sizing: border-box; */
        }

        .item-content {
            padding-left: 5px;
        }

        .item-big {
            flex: 5;
            /* align-items: center;
            display: flex;
            flex-direction: column;
            justify-content: left; */
        }

        .item-dark {
            color: white;
            background-color: gray;
        }

        .item-double {
            flex: 2;
            /* display: flex;
            flex-direction: column;
            padding: 5px;
            border: 1px solid #ddd;
            box-sizing: border-box; */
        }

        .item-title {
            font-weight: bold;
        }

        .row {
            display: flex;
            flex-direction: row;
            gap: 0px;
            margin: 0px;
        }

        .box {
            display: flex;
            height: 150px; 
        }

        .box-image {
            flex: 2;
            border: none;
            /* align-self: stretch; */
        }
        
        .box-red {
            flex: 4;
            background-color:  #E12922;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .box-center {
            color: white;
            font-weight: bold;
            font-size: 2em;           
        }

        .section {
            border: 1px solid #ddd;
            padding: 0px;
            margin: 0px;
            display: flex;
            flex-direction: column;
        }

        .section-title {
            background-color: #E12922;
            color: white;
            padding: 10px;
            text-align: center;
            font-weight: bold;
            margin: 0px;
        }

        @media (max-width: 600px) {
            .row {
                flex-direction: column;
            }

            .item {
                padding: 5px 0;
            }
        }
    </style>

</head>

<body>

    <div class="container">

        <!-- Order -->

        <div class="box">
            <div class="box-image">
                <img id="img_logo" src="{{ asset('images/logomail.png') }}" alt="SED INTERNATIONAL">
            </div>
            <div class="box-red">
                <div class="box-center">ORDEN EN LINEA SED</div>
            </div>
        </div>
        <div class="section">
            <div class="row">
                <div class="section item">
                    <span class="item-title">Orden</span>
                    <span class="item-content">{{ $order['order_number'] }}</span>
                </div>
                <div class="section item">
                    <span class="item-title">Fecha</span>
                    <span class="item-content"> {{ date('Y-m-d') }}</span>
                </div>
                <div class="section item-double">
                    <span class="item-title">Cuenta</span>
                    <span class="item-content">{{ $nit }} - {{ $customer }}</span>
                </div>
                <div class="section item-double">
                    <span class="item-title">Corrreo Cuenta</span>
                    <span class="item-content">{{ $customer_mail }}</span>
                </div>
            </div>
            <div class="row">
                <div class="section item">
                    <span class="item-title">ID orden</span>
                </div>
                <div class="section item">
                    <span class="item-content">{{ $order['trade_request_code'] }} <span class="item-title">Estado:
                        </span>{{ $order['request_status'] }} </span>
                </div>
                <div class="section item">
                    <span class="item-title">Transacción CUS</span>
                </div>
                <div class="section item">
                    <span class="item-content">{{ $order['transaction_cus'] }}</span>
                </div>
                <div class="section item">
                    <span class="item-title">Fecha CUS</span>
                </div>
                <div class="section item">
                    <span class="item-content">{{ $order['transaction_date_time'] }}</span>
                </div>
            </div>
            <div class="row">
                <div class="section item">
                    <span class="item-title">Notas</span>
                </div>
                <div class="section item-big">
                    <span class="item-content">{{ $order['notes'] }}</span>
                </div>
            </div>
        </div>
        <!-- Buyer -->
        <div class="section-title">Contacto</div>
        <div class="row">
            <div class="section item">
                <span class="item-title">Comprador</span>
            </div>
            <div class="section item-double"> 
                <span class="item-content">{{ $order['buyer_name'] }}</span>
            </div>
            <div class="section item">
                <span class="item-title">Correo</span>
            </div>
            <div class="section item-double">
                <span class="item-content">{{ $order['buyer_email'] }}</span>
            </div>
        </div>

        <div class="row">
            <div class="section item">
                <span class="item-title">Recibe</span>
            </div>
            <div class="section item-double">
                <span class="item-content">{{ $order['receiver_name'] }}</span>
                <span class="item-title">Teléfono: </span> <span>{{ $order['receiver_phone'] }}</span>
            </div>
            <div class="section item">
                <span class="item-title">Dirección:</span>
            </div>
            <div class="section item-double">
                <span class="item-content">{{ $order['receiver_address'] }}</span><br />
                <span class="item-content">Cod. País: {{ $order['receiver_country_id'] }}
                    Cod. Dpto: {{ $order['receiver_department_id'] }}
                    Cod. Ciudad: {{ $order['receiver_country_id'] }}</span>
            </div>
        </div>
        <div class="row">
            <div class="section item-double">
                <span class="item-title">Entrega</span>
                <span class="item-content">Propósito: {{ $order['delivery_purpose'] }} Tipo:
                    {{ $order['delivery_type'] }}</span>
            </div>
            <div class="section item-double">
                <span class="item-title">Fletes</span>
                <span class="item-content">{{ number_format($order['delivery_extra_cost'], 2) }} IVA:
                    {{ number_format($order['delivery_extra_cost_tax'], 2) }}</span>
            </div>
            <div class="section item-double">
                <span class="item-title">Transportadora </span>
                <span class="item-content">{{ $order['transport_company'] }} Tipo: {{ $order['transport_type'] }}</span>
            </div>
        </div>

        <div class="row">
            <div class="section item">
                <span class="item-title">Cupón</span>
            </div>
            <div class="section item-big">
                <span class="item-content">Promoción: {{ $order['coupon_id'] }} - {{ $order['coupon_name'] }} </span>
                <span class="item-content"> Valor: {{ number_format($order['coupon_value'], 2) }}
                    {{ $order['coupon_currency'] }}
                    Fecha: {{ $order['coupon_date'] }}</span>
            </div>
        </div>

        <!-- Products -->
        <div class="section-title">Productos</div>
        <div class="row">
            <div class="section item-double item-dark">
                <span class="item-title">SKU + Producto</span>
            </div>
            <div class="section item item-dark">
                <span class="item-title">Cantidad</span>
            </div>
            <div class="section item item-dark">
                <span class="item-title">Valor Unitario</span>
            </div>
            <div class="section item item-dark">
                <span class="item-title">Valor Total</span>
            </div>
            <div class="section item item-dark">
                <span class="item-title">Impuestos</span>
            </div>
        </div>
        @foreach ($order['items'] as $item)
            <div class="row">
                <div class="section item-double">
                    <h3>REF. {{ $item['part_num'] }} - {{ $item['brand'] }} : {{ $item['product_name'] }}</h3>
                </div>
                <div class="section item">
                    <span class="item-content">{{ number_format($item['quantity']) }}</span>
                </div>
                <div class="section item">
                    <span class="item-content">{{ number_format($item['sed_unit_price'], 2) }}
                        {{ $item['currency'] }}</span>
                </div>
                <div class="section item">
                    <span class="item-content">{{ number_format($item['sed_total_price'], 2) }}
                        {{ $item['currency'] }}</span>
                </div>
                <div class="section item">
                    <span class="item-content">{{ number_format($item['sed_tax_value'], 2) }} {{ $item['currency'] }}</span>
                </div>
            </div>
        @endforeach

    </div>


</body>

</html>