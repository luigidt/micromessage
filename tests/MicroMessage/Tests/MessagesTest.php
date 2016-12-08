<?php

namespace MicroMessage\Tests;

class MessagesTest extends ApplicationTest
{
    public function testListMessages()
    {
        $client = $this->createClient();
        $client->request('GET', '/messages/');
        $this->assertTrue($client->getResponse()->isOk());
        $this->assertEquals(
            $client->getResponse()->getContent(),
            "messages"
        );
    }
}
