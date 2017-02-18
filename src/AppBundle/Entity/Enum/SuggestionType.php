<?php

namespace AppBundle\Entity\Enum;

use Grachevko\Enum\Enum;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class SuggestionType extends Enum
{
    const SUGGESTION = 1;
    const CLAIM = 2;

    protected static $name = [
        self::SUGGESTION => 'Предложение',
        self::CLAIM => 'Претензия',
    ];
}
