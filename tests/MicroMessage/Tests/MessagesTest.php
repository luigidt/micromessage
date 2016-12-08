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

        $response = json_decode($this->client->getResponse()->getContent());
        $this->assertCount(1, $response);

        $message = $response[0];
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
}
