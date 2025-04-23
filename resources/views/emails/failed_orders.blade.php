<!DOCTYPE html>
<html>
<head>
    <title>{{ $data['subject'] }}</title>
</head>
<body>
    <h1>{{ $data['message'] }}</h1>
    <p>Customer: {{ $data['customer'] }}</p>
    <p>Email: {{ $data['customer_mail'] }}</p>
    <p>NIT: {{ $data['nit'] }}</p>

    <h2>Failed Orders:</h2>
    <ul>
        @foreach ($data['failed_orders'] as $order)
            <li>
                <strong>Error:</strong> {{ $order['error'] }}<br>
                <strong>Trade Request Code:</strong> {{ $order['trade_request_code'] }}<br>
                <strong>Buyer Name:</strong> {{ $order['buyer_name'] }}<br>
                <strong>Transaction Date:</strong> {{ $order['transaction_date_time'] }}<br>
                <strong>Transaction CUS:</strong> {{ $order['transaction_cus'] }}<br>
            </li>

            @if (isset($order['errors']) && count($order['errors']) > 0)
                <p>Errors validations to fix: {{ count($order['errors']) }}</p>
                <ul>
                    @foreach ($order['errors'] as $field => $messages)
                        <li>
                            <strong>{{ ucfirst(str_replace('_', ' ', $field)) }}:</strong>
                            <ul>
                                @foreach ($messages as $message)
                                    <li>{{ $message }}</li>
                                @endforeach
                            </ul>
                        </li>
                    @endforeach
                </ul>
            @endif
        @endforeach
    </ul>
</body>
</html>
