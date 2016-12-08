<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use MicroMessage\Entities\Message;

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

$messages->post('/', function (Request $request) use ($app) {
    $message = new Message();
    $message->setAuthor($request->get('author'));
    $message->setMessage($request->get('message'));

    $app['orm.em']->persist($message);
    $app['orm.em']->flush();

    return new JsonResponse(['id' => $message->getId()], 201, [
        'Location' => "/messages/{$message->getId()}"
    ]);
});

return $messages;
