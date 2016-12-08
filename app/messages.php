<?php

$messages = $app['controllers_factory'];

$messages->get('/', function () use ($app) {
    $messages = $app['orm.em']
        ->getRepository('MicroMessage\Entities\Message')
        ->findAll();

    return $app->json($messages);
});

return $messages;
