<?php

return array_merge(
    require __DIR__ . '/production.php',
    [
        'debug' => true,
        'orm.auto_generate_proxies' => true,
        'db.options' => [
            'url' => 'sqlite:///app.db'
        ],
        'cache.provider' => null
    ]
);
