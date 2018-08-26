<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Entity\News;
use Generator;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
class SmokeTest extends WebTestCase
{
    /**
     * @dataProvider pages
     */
    public function testIndex(string $uri): void
    {
        $client = static::createClient();
        $client->request('GET', $uri);
        $response = $client->getResponse();

        static::assertTrue($response->isSuccessful());
    }

    public function pages(): Generator
    {
        yield ['/'];
        yield ['/payment'];
        yield ['/reports'];
        yield ['/suggestions'];
        //yield ['/area'];
        yield ['/login'];
        yield ['/registration'];
        yield ['/reports'];
        //yield ['/reports/new'];
        //yield ['/reports/1'];

        /** @var News $newsItem */
        $newsItem = static::createClient()->getContainer()->get('doctrine')->getRepository(News::class)->findOneBy([]);

        yield ['/news/'.$newsItem->getSlug()];
    }
}
