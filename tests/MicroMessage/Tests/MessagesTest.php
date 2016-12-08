<?php

namespace MicroMessage\Tests;

class MessagesTest extends ApplicationTest
{
    private $message;

    public function setUp()
    {
        parent::setUp();
        $this->message = $this->getReference('message');
    }

    public function testListMessages()
    {
        $this->client->request('GET', '/messages/');
        $this->assertTrue($this->client->getResponse()->isOk());

        $content = json_decode($this->client->getResponse()->getContent());
        $this->assertCount(1, $content);
        $message = $content[0];

        $this->assertEquals($this->message->getId(), $message->id);
        $this->assertEquals($this->message->getAuthor(), $message->author);
        $this->assertEquals($this->message->getMessage(), $message->message);
    }

    public function testListMessagesWhenEmpty()
    {
        // Remove the fixture
        $this->app['orm.em']->remove($this->message);
        $this->app['orm.em']->flush();

        $this->client->request('GET', '/messages/');
        $this->assertTrue($this->client->getResponse()->isOk());

        $response = json_decode($this->client->getResponse()->getContent());
        $this->assertCount(0, $response);
    }

    public function testCreateNewMessage()
    {
        $this->client->request('POST', '/messages/', [
            'author' => 'Mary Doe',
            'message' => 'Hey John! Where are you?'
        ]);
        $this->assertEquals(201, $this->client->getResponse()->getStatusCode());

        // Check database count changed
        $count = $this->app['orm.em']->getRepository('MicroMessage\Entities\Message')
            ->createQueryBuilder('m')
            ->select('count(m.id)')
            ->getQuery()
            ->getSingleScalarResult();
        $this->assertEquals(2, $count, "The entity count was not changed");

        // Get new entity
        $content = json_decode($this->client->getResponse()->getContent());
        $message = $this->app['orm.em']->getRepository('MicroMessage\Entities\Message')
            ->find($content->id);

        // Check Location header
        $location = $this->client->getResponse()->headers->get('Location');
        $this->assertEquals("/messages/{$message->getId()}", $location);

        // Check properties are correct
        $this->assertEquals('Mary Doe', $message->getAuthor());
        $this->assertEquals('Hey John! Where are you?', $message->getMessage());
    }
}
