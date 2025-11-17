<?php 

$env = new App\Utils\Env();
$env->loadEnv(__DIR__ . '/.env');

return [
    'db' => [
        'driver' => 'pgsql',
        'host' => getenv('DB_HOST'),
        'port' => getenv('DB_PORT'),
        'database' => getenv('DB_NAME'),
        'username' => getenv('DB_USERNAME'),
        'password' => getenv('DB_PASSWORD'),
    ],
    'jwt' => [
        'secret' => getenv('SECRET_KEY_JWT'),
    ],
    'payment' => [
        'access_token' => getenv('MERCADOPAGO_ACCESS_TOKEN'),
        'public_key' => getenv('MERCADO_PAGO_PUBLIC_KEY'),
    ],
];