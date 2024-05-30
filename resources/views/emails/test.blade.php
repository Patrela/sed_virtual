<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Mail Template</title>
    <style type="text/css">
    h1 { font-size:3em; font-weight:bolder;color: aqua;}
    a{ background-color:darkblue; color: aqua; padding: 15px 25px; text-decoration: none;}
    </style>
</head>
<body>
    <h1>Hi, {{ $user}}</h1>
    <p>This is the first one email to you </p>
    <p>    <a href="{{ $home_link }}">nuestro Blog</a> </p>
    <p>See you!</p>
</body>
</html>
