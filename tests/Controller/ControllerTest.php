<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Kernel;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
class ControllerTest extends WebTestCase
{
    public function testIndex(): void
    {
        $app = new Kernel('test', false);
        $app->boot();

        foreach (['/contacts', '/suggestions', '/payment', '/reports'] as $url) {
            self::assertTrue($app->handle(Request::create($url))->isSuccessful());
        }
    }
}
