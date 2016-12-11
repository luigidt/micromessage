<?php

/**
 * Neste arquivo estão todas as rotas relacionadas ao recurso mensagem,
 * case sejam adicionados novos recursos, eles deveriam ir em um novo arquivo
 */

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\Tools\Pagination\Paginator;

use MicroMessage\Entities\Message;
use MicroMessage\Helpers\ViolationsHelper;

$messages = $app['controllers_factory'];

/**
 * GET /
 * Retorna uma lista com todas as mensagens publicadas
 * PARÂMETROS:
 *  - page int opcional
 *     Página de resultados que deve ser retornada
 *     O valor padrão para esse parâmetro é 100
 *  - limit int opcional
 *     Quantidade de resultados que deve ser retornado em cada página
 *
 * Exemplo de resposta:
 *
 * {
 *     "messages": [
 *         {
 *             "author": "John Doe",
 *             "message": "Hello people!",
 *         },
 *         {
 *             "author": "John Doe",
 *             "message": "Hello people!",
 *         },
 *     ]
 * }
 *
 * Se os parâmetros page ou limit forem informados o resultado será no formato
 * {
 *     "messages": [
 *         {
 *             "author": "John Doe",
 *             "message": "Hello people!",
 *         },
 *         ....
 *     ],
 *     "limit": 100,  // limite informado no parâmetro ou o valor padrão
 *     "size": 100,   // quantidade de resultados informados
 *     "start": 0     // posição em que a paginação iniciou
 * }
 */
$messages->get('/', function (Request $request) use ($app) {
    $query = $app['orm.em']
        ->createQuery('SELECT m.id, m.author, m.message FROM MicroMessage\Entities\Message m')
        ->setHydrationMode(\Doctrine\ORM\AbstractQuery::HYDRATE_ARRAY);

    if ($request->get('page', null) === null && $request->get('limit', null) === null) {
        $messages = $query->getResult();
        return $app->json(['messages' => $messages]);
    }

    $page = max(0, $request->get('page', 0));
    $limit = max(0, $request->get('limit', 100));
    $start = $limit * $page; // $limit * $page - 1

    $query->setFirstResult($start)
        ->setMaxResults($limit);

    $messages = $query->getResult();

    return $app->json([
        'messages' => $messages,
        'limit' => $limit,
        'size' => count($messages),
        'start' => $start
    ]);
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
