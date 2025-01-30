<?php

return [
    'base_url' => env('AD_AUTH_BASE_URL', 'https://dominio.com/auth/endpoint'),
    'timeout' => env('AD_AUTH_TIMEOUT', 30),
    'application_code' => env('AD_AUTH_APPLICATION_CODE', 'SIGLA'),
    'auth_key_type' => env('AD_AUTH_KEY_TYPE', '0'),
];