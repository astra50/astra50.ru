<?php

declare(strict_types=1);

namespace App\Entity\Enum;

use Grachevko\Enum\Enum;

/**
 * @method static self area()
 * @method static self size()
 * @method static self share()
 * @method bool   isArea()
 * @method bool   isSize()
 * @method bool   isShare()
 *
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class Calculation extends Enum
{
    private const AREA = 1;
    private const SIZE = 2;
    private const SHARE = 3;

    protected static $name = [
        self::AREA => 'На участок',
        self::SIZE => 'На Сотку',
        self::SHARE => 'Разделить между участками',
    ];

    protected static $columnName = [
        self::AREA => 'За участок',
        self::SIZE => 'За сотку',
        self::SHARE => 'Поровну',
    ];
}
