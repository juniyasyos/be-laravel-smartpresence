<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'],

    'allowed_origins' => ['http://localhost:5173', 'http://localhost:5174', 'http://localhost:3000', "http://192.168.1.37:3000"],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['content-type', 'x-xsrf-token', 'authorization', 'accept'],

    'exposed_headers' => [],

    'max_age' => 86400,

    'supports_credentials' => true,

];