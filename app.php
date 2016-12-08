<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = new Silex\Application();

$app->get('/', function () {
    return "it's working";
});

$app->mount('/messages', require __DIR__ . '/app/messages.php');

return $app;
