<?php

declare(strict_types=1);

namespace Grachev\TokenBundle;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
interface PayloadsProviderInterface
{
    /**
     * @param string $token
     *
     * @return array|null
     */
    public function payloads(string $token): ?array;
}
