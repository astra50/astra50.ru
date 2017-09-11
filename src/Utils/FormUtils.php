<?php

declare(strict_types=1);

namespace App\Utils;

use Ramsey\Uuid\UuidInterface;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class FormUtils
{
    /**
     * @param array  $array
     * @param string $displayField
     * @param string $valueField
     *
     * @return array
     */
    public static function arrayToChoices(array $array, string $displayField, string $valueField = 'id'): array
    {
        $result = [];
        foreach ($array as $key => $item) {
            $id = $item[$valueField];

            $display = $item[$displayField];
            $value = $id instanceof UuidInterface ? $id->toString() : $id;

            $result[$display] = $value;
        }

        return $result;
    }
}
