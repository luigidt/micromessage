<?php

require_once __DIR__ . '/vendor/autoload.php';

use Dflydev\Provider\DoctrineOrm\DoctrineOrmServiceProvider;

$enviroment = getenv('MICROMESSAGE_ENV') ?: 'development';
$config = require __DIR__ . "/config/" . $enviroment . ".php";

$app = new Silex\Application();
$app['debug'] = $config['debug'];

$app->register(
    new Silex\Provider\DoctrineServiceProvider(),
    ['db.options' => $config['db.options']]
);

$app->register(new DoctrineOrmServiceProvider, array(
    'orm.proxies_dir' => __DIR__ . '/var/proxies',
    'orm.auto_generate_proxies' => $config['orm.auto_generate_proxies'],
    'orm.em.options' => array(
        'mappings' => array(
            array(
                'type' => 'annotation',
                'namespace' => 'MicroMessage\Entities',
                'path' => __DIR__ . '/src/MicroMessage/Entities',
            ),
        ),
        'metadata_cache' => $config['cache.provider'],
        'hydration_cache' => $config['cache.provider'],
        'query_cache' => $config['cache.provider']
    ),
));

$app->register(new Silex\Provider\ValidatorServiceProvider());

$app->mount('/messages', require __DIR__ . '/app/messages.php');

return $app;
