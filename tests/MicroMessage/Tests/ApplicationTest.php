<?php

namespace MicroMessage\Tests;

use Silex\WebTestCase;

abstract class ApplicationTest extends WebTestCase
{
    public function createApplication()
    {
        return require __DIR__ . '/../../../app.php';
    }
}
