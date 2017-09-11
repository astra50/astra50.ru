<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Entity\News;
use App\Kernel;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
class NewsControllerTest extends WebTestCase
{
    public function testIndex(): void
    {
        $app = new Kernel('test', false);
        $app->boot();

        /** @var News $news */
        $news = $app->getContainer()->get('app.repository.news')->createQueryBuilder('n')
            ->setMaxResults(1)
            ->getQuery()->getSingleResult();

        self::assertTrue($app->handle(Request::create('/'))->isSuccessful());
        self::assertTrue($app->handle(Request::create('/news/'.$news->getSlug()))->isSuccessful());
    }
}
