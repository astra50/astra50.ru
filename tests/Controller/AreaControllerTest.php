<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Kernel;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
class AreaControllerTest extends WebTestCase
{
    public function testIndex(): void
    {
        $app = new Kernel('test', false);
        $app->boot();

        self::assertTrue($app->handle(Request::create('/area'))->isSuccessful());
    }
}
