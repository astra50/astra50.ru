<?php

namespace AppBundle\Doctrine;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Ramsey\Uuid\Uuid;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class UuidBinaryType extends \Ramsey\Uuid\Doctrine\UuidBinaryType
{
    /**
     * {@inheritdoc}
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        try {
            return Uuid::fromBytes($value)->getBytes();
        } catch (\Exception $e) {
        }

        return parent::convertToDatabaseValue($value, $platform);
    }
}
