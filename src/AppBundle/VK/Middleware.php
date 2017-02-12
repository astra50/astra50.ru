<?php

namespace AppBundle\VK;

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
            return json_decode($response->getBody(), true)['response'];
        };
    }
}
