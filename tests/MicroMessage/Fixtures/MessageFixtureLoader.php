<?php

namespace MicroMessage\Fixtures;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;

use MicroMessage\Entities\Message;

class MessageFixtureLoader extends AbstractFixture
{
    public function load(ObjectManager $manager)
    {
        $message = new Message();
        $message->setAuthor('John Doe');
        $message->setMessage('Hello there everyone!');

        $manager->persist($message);
        $manager->flush();
        $this->addReference('message', $message);
    }
}
