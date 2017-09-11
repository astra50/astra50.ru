<?php

declare(strict_types=1);

namespace App\VK;

use GuzzleHttp\Psr7\Response;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class Middleware
{
    /**
     * @return \Closure
     */
    public static function decodeResponse(): \Closure
    {
        return function (Response $response) {
            return json_decode($response->getBody()->getContents(), true)['response'];
        };
    }
}
