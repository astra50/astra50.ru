<?php

declare(strict_types=1);

namespace App\Entity\Enum;

use Grachevko\Enum\Enum;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class Financing extends Enum
{
    private const BUDGET = 1;
    private const VOLUNTARY = 2;
    private const COMBINED = 3;

    protected static $name = [
        self::BUDGET => 'Бюджет',
        self::VOLUNTARY => 'Добровольное',
        self::COMBINED => 'Совмещенное',
    ];
}
