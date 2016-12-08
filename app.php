<?php

require_once __DIR__ . '/vendor/autoload.php';

use Dflydev\Provider\DoctrineOrm\DoctrineOrmServiceProvider;

$app = new Silex\Application();
$app['debug'] = true;

$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    'db.options' => array(
        'driver'   => 'pdo_sqlite',
        'path'     => __DIR__ . '/app.db',
    ),
));

$app->register(new DoctrineOrmServiceProvider, array(
    'orm.proxies_dir' => __DIR__ . '/var/proxies',
    'orm.em.options' => array(
        'mappings' => array(
            array(
                'type' => 'annotation',
                'namespace' => 'MicroMessage\Entities',
                'path' => __DIR__ . '/src/MicroMessage/Entities',
            ),
        ),
    ),
));

$app->get('/', function () {
    return "it's working";
});

$app->mount('/messages', require __DIR__ . '/app/messages.php');

return $app;
