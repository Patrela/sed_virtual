<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'], //['api/*','*'],
    'allowed_methods' => ['*'],
    'allowed_origins' => ['http://localhost','http://127.0.0.1', 'https://www.postman.com', 'https://www.sed.international/', '*.mydomain.de'], // , '*'
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true,  //false,
];
