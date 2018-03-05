<?php

declare(strict_types=1);

namespace App\Entity\Enum;

use Grachevko\Enum\Enum;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class SuggestionType extends Enum
{
    private const SUGGESTION = 1;
    private const CLAIM = 2;
    private const REPORT_COMMENT = 3;
    private const RECEIPT_REQUEST = 4;

    protected static $name = [
        self::SUGGESTION => 'Предложение',
        self::CLAIM => 'Претензия',
        self::REPORT_COMMENT => 'Замечания по отчетности',
        self::RECEIPT_REQUEST => 'Заказ квитанции на оплату',
    ];
}
