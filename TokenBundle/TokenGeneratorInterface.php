<?php

declare(strict_types=1);

namespace Grachev\TokenBundle;

use DateTimeInterface;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
interface TokenGeneratorInterface
{
    /**
     * @param array             $payloads
     * @param DateTimeInterface $expiredAt
     *
     * @return string
     */
    public function lifetime(array $payloads, DateTimeInterface $expiredAt): string;

    /**
     * @param array                  $payloads
     * @param DateTimeInterface|null $expiredAt
     *
     * @return string
     */
    public function disposable(array $payloads, DateTimeInterface $expiredAt = null): string;
}
