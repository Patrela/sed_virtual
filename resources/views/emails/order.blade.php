<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ONLINE ORDER SED</title>
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

        span {
            padding: 6px;
            font-size: 17px !important;
            font-weight: bolder;
        }

        .card {
            display: flex;
            flex-direction: row;
            gap: 10px;
        }

        .card-image {
            width: 240px;
            /* 310 */
            margin: 2px;
            padding: 5px;
            border: none;
            /* none; 1px solid black; */
            height: 220px;
            /* 310 */
        }

        .card-image img {
            margin: 0;
            padding: 1px;
            width: 238px;
            /* 298 */
            height: 218px;
            /* 298 */
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
                <h1>ORDER EN LINEA SED Nº {{ $order['order_number'] }}</h1>
            </div>
        </div>
        <detail>
            <br>
            <h2>Order {{ $order['order_number'] }}</h2>
            <p>Fecha: {{ date('Y-m-d') }}<br />
                <strong>Cliente: {{ $nit }} - {{ $customer }}</strong><br />
                <span>Correo</span> {{ $customer_mail }}<br />
                <span>buyer_name</span> {{ $order['buyer_name'] }} | <span>buyer_email</span> {{ $order['buyer_email'] }}<br />
                <span>trade_request_code</span> {{ $order['trade_request_code'] }}<br />
                <span>request_status</span> {{ $order['request_status'] }}<br />
                <span>transaction_cus</span> {{ $order['transaction_cus'] }} | <span>transaction_date_time</span> {{ $order['transaction_date_time'] }}<br />
                <span>notes</span> {{ $order['notes'] }}
            </p>
        </detail>

        @foreach ($order['items'] as $item)
            <div class="card">
                <div class="card-content">
                    <h3>REF. {{ $item['part_num'] }} - {{ $item['brand'] }} : {{ $item['product_name'] }}</h3>
                    <p>
                        <span>quantity</span> {{ number_format($item['quantity']) }} | <span>currency</span> {{ $item['currency'] }} <br />
                        <span>unit_price</span> {{ number_format($item['unit_price']) }} | <span>total_price</span> {{ number_format($item['total_price']) }} <br />
                        <span>tax_value</span> {{ number_format($item['tax_value']) }} <br />
                    </p>
                </div>
            </div>
        @endforeach

        <detail>
            <p>
                <span>receiver_name</span> {{ $order['receiver_name'] }}<br /> | <span>receiver_phone</span> {{ $order['receiver_phone'] }}<br />
                <span>receiver_address</span> {{ $order['receiver_address'] }}<br />
                <span>receiver_department_id</span> {{ $order['receiver_department_id'] }} | <span>receiver_country_id</span> {{ $order['receiver_country_id'] }}<br />
                <span>delivery_purpose</span> {{ $order['delivery_purpose'] }}<br />
                <span>delivery_type</span> {{ $order['delivery_type'] }}<br />
                <span>delivery_extra_cost</span> {{ $order['delivery_extra_cost'] }} | <span>delivery_extra_cost_tax</span> {{ $order['delivery_extra_cost_tax'] }}<br />
                <span>transport_type</span> {{ $order['transport_type'] }} | <span>transport_company</span> {{ $order['transport_company'] }}<br />
            </p>
            <p>
                <span>coupon_id</span> {{ $order['coupon_id'] }} | <span>coupon_name</span> {{ $order['coupon_name'] }}<br />
                <span>coupon_value</span> {{ $order['coupon_value'] }} | <span>coupon_date</span> {{ $order['coupon_date'] }}<br />
                <span>coupon_currency</span> {{ $order['coupon_currency'] }}<br />
                <span></span> {{ $order[''] }}
            </p>
        </detail>

        <br />
        <br />
    </div>
</body>

</html>
