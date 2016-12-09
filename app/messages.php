<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use MicroMessage\Entities\Message;
use MicroMessage\Helpers\ViolationsHelper;

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
    $message->setAuthor($request->get('author', null));
    $message->setMessage($request->get('message', null));

    $violations = $app['validator']->validate($message);

    if (count($violations) > 0) {
        return $app->json(ViolationsHelper::toJson($violations), 400);
    }

    $app['orm.em']->persist($message);
    $app['orm.em']->flush();

    return new JsonResponse(['id' => $message->getId()], 201, [
        'Location' => "/messages/{$message->getId()}"
    ]);
});

$messages->get('/{id}', function ($id) use ($app) {
    try {
        $messages = $app['orm.em']
            ->createQueryBuilder()
            ->from('MicroMessage\Entities\Message', 'm')
            ->select(['m.id', 'm.author', 'm.message'])
            ->where('m.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getSingleResult(\Doctrine\ORM\AbstractQuery::HYDRATE_ARRAY);

        return $app->json($messages);
    } catch (\Doctrine\Orm\NoResultException $e) {
        return new Response('', 404);
    }
});

$messages->delete('/{id}', function ($id) use ($app) {
    $message = $app['orm.em']
        ->getRepository('MicroMessage\Entities\Message')
        ->find($id);

    if ($message === null) {
        return new Response('', 404);
    }

    $app['orm.em']->remove($message);
    $app['orm.em']->flush();

    return new Response('', 204);
});

$messages->put('/{id}', function (Request $request, $id) use ($app) {
    $message = $app['orm.em']
        ->getRepository('MicroMessage\Entities\Message')
        ->find($id);

    if ($message === null) {
        return new Response('', 404);
    }

    $message->setAuthor($request->get('author', null));
    $message->setMessage($request->get('message', null));

    $violations = $app['validator']->validate($message);

    if (count($violations) > 0) {
        return $app->json(ViolationsHelper::toJson($violations), 400);
    }

    $app['orm.em']->persist($message);
    $app['orm.em']->flush();

    return new Response('', 204);
});

return $messages;
