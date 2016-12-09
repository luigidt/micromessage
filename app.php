<?php

require_once __DIR__ . '/vendor/autoload.php';

use Dflydev\Provider\DoctrineOrm\DoctrineOrmServiceProvider;

$app = new Silex\Application();

$app['debug'] = getenv('MICROMESSAGE_ENV') != 'production';

$connectionOptions = [
    'driver'    => 'pdo_pgsql',
    'host'      => getenv('POSTGRES_PORT_5432_TCP_ADDR'),
    'port'      => getenv('POSTGRES_PORT_5432_TCP_PORT'),
    'dbname'    => 'micromessage',
    'user'      => 'postgres',
    'password'  => getenv('POSTGRES_1_ENV_POSTGRES_PASSWORD'),
];

$cacheProvider = [
    'driver' => 'redis',
    'host' => getenv('REDIS_1_PORT_6379_TCP_ADDR'),
    'port' => getenv('REDIS_1_PORT_6379_TCP_PORT')
];

if (getenv('MICROMESSAGE_ENV') != 'production') {
    $connectionOptions = ['url' => 'sqlite:///app.db'];
    $cacheProvider = null;
}

$app->register(new Silex\Provider\DoctrineServiceProvider(), [
    'db.options' => $connectionOptions ]);

$app->register(new DoctrineOrmServiceProvider, array(
    'orm.proxies_dir' => __DIR__ . '/var/proxies',
    'orm.auto_generate_proxies' => getenv('MICROMESSAGE_ENV') != 'production',
    'orm.em.options' => array(
        'mappings' => array(
            array(
                'type' => 'annotation',
                'namespace' => 'MicroMessage\Entities',
                'path' => __DIR__ . '/src/MicroMessage/Entities',
            ),
        ),
        'metadata_cache' => $cacheProvider,
        'hydration_cache' => $cacheProvider,
        'query_cache' => $cacheProvider
    ),
));

$app->register(new Silex\Provider\ValidatorServiceProvider());

$app->mount('/messages', require __DIR__ . '/app/messages.php');

return $app;
