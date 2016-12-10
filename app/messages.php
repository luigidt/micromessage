<?php

/**
 * Neste arquivo estão todas as rotas relacionadas ao recurso mensagem,
 * case sejam adicionados novos recursos, eles deveriam ir em um novo arquivo
 */

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use MicroMessage\Entities\Message;
use MicroMessage\Helpers\ViolationsHelper;

$messages = $app['controllers_factory'];

/**
 * GET /
 * Retorna uma lista com todas as mensagens publicadas
 * Exemplo de resposta:
 *
 * [
 *     {
 *         "author": "John Doe",
 *         "message": "Hello people!",
 *     },
 *     {
 *         "author": "John Doe",
 *         "message": "Hello people!",
 *     },
 * ]
 *
 */
$messages->get('/', function () use ($app) {
    $messages = $app['orm.em']
        ->createQuery('SELECT m.id, m.author, m.message FROM MicroMessage\Entities\Message m')
        ->getResult(\Doctrine\ORM\AbstractQuery::HYDRATE_ARRAY);

    return $app->json($messages);
});

/**
 * POST /
 * Adiciona uma nova mensagem
 * PARÂMETROS:
 *  - author string(32) obrigatório
 *  - message string(140) obrigatório
 */
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

/**
 * GET /{id}
 * Busca uma mensagem específica
 * PARÂMETROS:
 *  - id int obrigatório
 *
 * Exemplo de resposta:
 *
 * {
 *     "author": "John Doe",
 *     "message": "Hello people!",
 * }
 *
 * Retorna o status 404 se a mensagem não for encontrada
 */
$messages->get('/{id}', function ($id) use ($app) {
    try {
        // Usando uma query para especificar os campos que devem ser retornados no JSON
        $messages = $app['orm.em']
            ->createQuery('SELECT m.id, m.author, m.message FROM MicroMessage\Entities\Message m WHERE m.id = :id')
            ->setParameter('id', $id)
            ->getSingleResult(\Doctrine\ORM\AbstractQuery::HYDRATE_ARRAY);

        return $app->json($messages);
    } catch (\Doctrine\Orm\NoResultException $e) {
        return new Response('', 404);
    }
});

/**
 * DELETE /{id}
 * Remove uma mensagem específica
 * PARÂMETROS:
 *  - id int obrigatório
 *
 * Retorna o status 204 se a mensagem foi removida com sucesso
 * Retorna o status 404 se a mensagem não for encontrada
 */
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

/**
 * PUT /{id}
 * Atualiza uma mensagem existente (não permite criação de novas mensagens,
 * nesse WebService os ids devem ser gerados automaticamente)
 *
 * PARÂMETROS:
 *  - id int obrigatório
 *
 * PARÂMETROS POST:
 *  - author string(32) obrigatório
 *  - message string(140) obrigatório
 *
 * Retorna o status 204 se a mensagem foi atualizada com sucesso
 * Retorna o status 404 se a mensagem não for encontrada
 */
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
