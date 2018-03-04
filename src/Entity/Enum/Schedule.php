<?php

declare(strict_types=1);

namespace App\Entity\Enum;

use Grachevko\Enum\Enum;

/**
 * @method static self once()
 * @method static self monthly()
 * @method bool   isOnce()
 * @method bool   isMonthly()
 *
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class Schedule extends Enum
{
    private const ONCE = 1;
    private const MONTHLY = 2;

    protected static $name = [
        self::ONCE => 'Разово',
        self::MONTHLY => 'Ежемесячно',
    ];

    protected static $columnName = [
        self::ONCE => 'Разовая',
        self::MONTHLY => 'Ежемесячная',
    ];
}
