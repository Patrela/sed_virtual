<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => ['http://localhost','http://127.0.0.1','https://www.postman.com','https://www.sed.international','https://master-union.com', '*'],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['Origin', 'Accept', 'Content-Type', 'Authorization', 'X-Requested-With', 'X-Token-Auth', 'X-Auth-Token', 'X-CSRF-TOKEN', 'X-CSRF-Token', 'X-Custom-Header', 'x-api-key'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,
];
