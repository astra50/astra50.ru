<?php

declare(strict_types=1);

namespace Tests\App\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
class AreaControllerTest extends WebTestCase
{
    public function testIndex(): void
    {
        $app = new \AppKernel('test', false);
        $app->boot();

        self::assertTrue($app->handle(Request::create('/area'))->isSuccessful());
    }
}
