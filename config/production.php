<?php

return [
    'debug' => false,
    'orm.auto_generate_proxies' => false,
    'db.options' => [
        'driver'    => 'pdo_pgsql',
        'host'      => getenv('POSTGRES_PORT_5432_TCP_ADDR'),
        'port'      => getenv('POSTGRES_PORT_5432_TCP_PORT'),
        'dbname'    => 'micromessage',
        'user'      => 'postgres',
        'password'  => getenv('POSTGRES_1_ENV_POSTGRES_PASSWORD'),
    ],
    'cache.provider' => [
        'driver' => 'redis',
        'host' => getenv('REDIS_1_PORT_6379_TCP_ADDR'),
        'port' => getenv('REDIS_1_PORT_6379_TCP_PORT')
    ]
];
