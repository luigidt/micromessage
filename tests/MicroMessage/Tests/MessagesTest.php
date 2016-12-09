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

    public function testGetMessageById()
    {
        $this->client->request('GET', '/messages/' . $this->message->getId());
        $this->assertTrue($this->client->getResponse()->isOk());

        $message = json_decode($this->client->getResponse()->getContent());
        $this->assertEquals($this->message->getId(), $message->id);
        $this->assertEquals($this->message->getAuthor(), $message->author);
        $this->assertEquals($this->message->getMessage(), $message->message);
    }

    public function testGetMessageByIdNotFound()
    {
        $this->client->request('GET', '/messages/99999');
        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
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

    public function testDontLetCreateMessageWithoutAuthor()
    {
        $this->client->request('POST', '/messages/', [
            'message' => 'Hey John! Where are you?'
        ]);
        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
        $errors = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals(
            ['errors' => [['property' => 'author', 'message' => 'This value should not be blank.']]],
            $errors
        );
    }

    public function testDontLetCreateMessageWhenAuthorIsTooLong()
    {
        $this->client->request('POST', '/messages/', [
            'author' => 'A Very Long Author Name That Should Not Work!',
            'message' => 'Hey John! Where are you?'
        ]);
        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
        $errors = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals(
            [
                'errors' => [
                    [
                        'property' => 'author',
                        'message' => 'This value is too long. It should have 32 characters or less.'
                    ]
                ]
            ],
            $errors
        );
    }

    public function testDontLetCreateMessageWithoutText()
    {
        $this->client->request('POST', '/messages/', [
            'author' => 'Mary Joe',
        ]);
        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
        $errors = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals(
            ['errors' => [['property' => 'message', 'message' => 'This value should not be blank.']]],
            $errors
        );
    }

    public function testDontLetCreateMessageWhenTextIsTooLong()
    {
        $this->client->request('POST', '/messages/', [
            'author' => 'Mary Joe',
            'message' => str_repeat('Hey John! Where are you?', 10)
        ]);
        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
        $errors = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals(
            [
                'errors' => [
                    [
                        'property' => 'message',
                        'message' => 'This value is too long. It should have 140 characters or less.'
                    ]
                ]
            ],
            $errors
        );
    }

    public function testRemoveMessage()
    {
        $this->client->request('DELETE', '/messages/' . $this->message->getId());
        $this->assertEquals(204, $this->client->getResponse()->getStatusCode());

        // Check entity count is zero
        $count = $this->app['orm.em']->getRepository('MicroMessage\Entities\Message')
            ->createQueryBuilder('m')
            ->select('count(m.id)')
            ->getQuery()
            ->getSingleScalarResult();

        $this->assertEquals(0, $count, 'Entity count should be zero');
    }

    public function testRemoveMessageNotFound()
    {
        $this->client->request('DELETE', '/messages/99999');
        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
    }

    public function testUpdateMessage()
    {
        $this->client->request('PUT', '/messages/' . $this->message->getId(), [
            'author' => 'John Doe Jr.',
            'message' => 'A new message!'
        ]);

        $this->assertEquals(204, $this->client->getResponse()->getStatusCode());

        $this->assertEquals('John Doe Jr.', $this->message->getAuthor());
        $this->assertEquals('A new message!', $this->message->getMessage());
    }

    public function testUpdateMessageNotFound()
    {
        $this->client->request('PUT', '/messages/99999', [
            'author' => 'John Doe Jr.',
            'message' => 'A new message!'
        ]);

        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
    }

    public function testDontLetUpdateMessageWithoutAuthor()
    {
        $this->client->request('PUT', '/messages/' . $this->message->getId(), [
            'message' => 'A new message!'
        ]);

        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
        $errors = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals(
            ['errors' => [['property' => 'author', 'message' => 'This value should not be blank.']]],
            $errors
        );
    }

    public function testDontLetUpdateMessageWhenAuthorIsTooLong()
    {
        $this->client->request('PUT', '/messages/' . $this->message->getId(), [
            'author' => 'Very Long Name With More Than 32 Characters',
            'message' => 'A new message!'
        ]);

        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
        $errors = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals(
            [
                'errors' => [
                    [
                        'property' => 'author',
                        'message' => 'This value is too long. It should have 32 characters or less.'
                    ]
                ]
            ],
            $errors
        );
    }

    public function testDontLetUpdateMessageWithoutText()
    {
        $this->client->request('PUT', '/messages/' . $this->message->getId(), [
            'author' => 'John Doe Jr',
            'message' => ''
        ]);

        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
        $errors = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals(
            ['errors' => [['property' => 'message', 'message' => 'This value should not be blank.']]],
            $errors
        );
    }

    public function testDontLetUpdateMessageWhenTextIsTooLong()
    {
        $this->client->request('PUT', '/messages/' . $this->message->getId(), [
            'author' => 'John Doe Jr',
            'message' => str_repeat('A new message! But too long :(', 10)
        ]);

        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
        $errors = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals(
            [
                'errors' => [
                    [
                        'property' => 'message',
                        'message' => 'This value is too long. It should have 140 characters or less.'
                    ]
                ]
            ],
            $errors
        );
    }
}
