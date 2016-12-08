<?php

namespace MicroMessage\Tests;

use Silex\WebTestCase;

abstract class ApplicationTest extends WebTestCase
{
    private $referenceRepository;
    protected $client;

    public function createApplication()
    {
        return require __DIR__ . '/../../../app.php';
    }

    public function setUp()
    {
        parent::setUp();
        $this->loadFixtures();
        $this->client = $this->createClient();
    }

    protected function getReference($name)
    {
        return $this->referenceRepository
            ->getReference($name);
    }

    private function loadFixtures()
    {
        $loader = new \Doctrine\Common\DataFixtures\Loader();
        $loader->loadFromDirectory(__DIR__ . '/../Fixtures');

        $em = $this->app['orm.em'];
        $purger = new \Doctrine\Common\DataFixtures\Purger\ORMPurger($em);
        $executor = new \Doctrine\Common\DataFixtures\Executor\ORMExecutor($em, $purger);

        $executor->execute($loader->getFixtures());
        $this->referenceRepository = $executor->getReferenceRepository();
    }
}
