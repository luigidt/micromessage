<?php

$messages = $app['controllers_factory'];

$messages->get('/', function () use ($app) {
    $messages = $app['orm.em']
        ->createQueryBuilder()
        ->from('MicroMessage\Entities\Message', 'm')
        ->select(['m.id', 'm.author', 'm.message'])
        ->getQuery()
        ->getResult(\Doctrine\ORM\AbstractQuery::HYDRATE_ARRAY);
    return $app->json($messages);
});

return $messages;
