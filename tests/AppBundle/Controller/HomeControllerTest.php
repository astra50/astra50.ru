<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
class HomeControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $app = new \AppKernel('test', false);
        $app->boot();

        self::assertTrue($app->handle(Request::create('/'))->isSuccessful());
        self::assertTrue($app->handle(Request::create('/underconstruction'))->isSuccessful());
    }
}
