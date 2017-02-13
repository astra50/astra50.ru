<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
class ControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $app = new \AppKernel('test', false);
        $app->boot();

        foreach (['/contacts', '/suggestions', '/payment', '/report'] as $url) {
            self::assertTrue($app->handle(Request::create($url))->isSuccessful());
        }
    }
}
