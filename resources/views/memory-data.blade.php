<!DOCTYPE html>
<html>
<head>
    <title>Session Data</title>
</head>
<body>
    <h1>Session Data</h1>
    <table border="1">
        <tr>
            <th>Key</th>
            <th>Value</th>
        </tr>
        @foreach ($session as $key => $value)
            <tr>
                <td>{{ $key }}</td>
                <td>{{ is_array($value) ? json_encode($value) : $value }}</td>
            </tr>
        @endforeach
    </table>
    <h1>User Data</h1>
    @if ($user)
        <table border="1">
            <tr>
                <th>Attribute</th>
                <th>Value</th>
            </tr>
            @foreach ($user->toArray() as $key => $value)
                <tr>
                    <td>{{ $key }}</td>
                    <td>{{ is_array($value) ? json_encode($value) : $value }}</td>
                </tr>
            @endforeach
        </table>
    @else
        <p>No authenticated user.</p>
    @endif
    <h1>Cache Data</h1>
    @if (!empty($cacheData))
        <table border="1">
            <tr>
                <th>Key</th>
                <th>Value</th>
            </tr>
            @foreach ($cacheData as $key => $value)
                <tr>
                    <td>{{ $key }}</td>
                    <td>{{ is_array($value) ? json_encode($value) : $value }}</td>
                </tr>
            @endforeach
        </table>
    @else
        <p>No cache data found.</p>
    @endif
</body>
</html>
