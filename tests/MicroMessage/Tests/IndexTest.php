<?php

namespace MicroMessage\Tests;

use Silex\WebTestCase;

class IndexTest extends WebTestCase
{
    public function createApplication()
    {
        return require __DIR__ . '/../../../app.php';
    }

    public function testIndexMethod()
    {
        $client = $this->createClient();
        $client->request('GET', '/');
        $this->assertTrue($client->getResponse()->isOk());
        $this->assertEquals(
            $client->getResponse()->getContent(),
            "it's working"
        );
    }
}
