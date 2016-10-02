<?php

namespace AppBundle\Doctrine;

use Ramsey\Uuid\UuidInterface;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class DoctrineUtils
{
    /**
     * @param array  $array
     * @param string $fieldName
     * @param string $fieldId
     *
     * @return array
     */
    public static function arrayToChoices(array $array, string $fieldName, string $fieldId = 'id'): array
    {
        $result = [];
        foreach ($array as $key => $item) {
            $id = $item[$fieldId];

            $result[$item[$fieldName]] = $id instanceof UuidInterface ? $id->toString() : $id;
        }

        ksort($result);

        return $result;
    }
}
