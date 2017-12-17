<?php

declare(strict_types=1);

namespace App\Entity\Enum;

use Grachevko\Enum\Enum;

/**
 * @method string getHeader()
 * @method static self financial()
 * @method static self accounting()
 * @method static self project()
 */
final class ReportType extends Enum
{
    private const FINANCIAL = 1;
    private const ACCOUNTING = 2;
    private const PROJECT = 3;

    protected static $name = [
        self::FINANCIAL => 'Финансовый',
        self::ACCOUNTING => 'Бухгалтерский',
        self::PROJECT => 'Проектный',
    ];

    protected static $header = [
        self::FINANCIAL => 'Сводная отчетность',
        self::ACCOUNTING => 'Бухгалтерские отчёты',
        self::PROJECT => 'Проектные отчёты',
    ];
}
