<?php

declare(strict_types=1);

namespace Grachevko\TokenBundle;

use DateTimeInterface;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
interface TokenGeneratorInterface
{
    public function lifetime(array $payloads, DateTimeInterface $expiredAt): string;

    public function disposable(array $payloads, DateTimeInterface $expiredAt = null): string;
}
