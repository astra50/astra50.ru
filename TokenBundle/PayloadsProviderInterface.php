<?php

declare(strict_types=1);

namespace Grachevko\TokenBundle;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
interface PayloadsProviderInterface
{
    public function payloads(string $token): ?array;
}
