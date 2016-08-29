<?php

namespace AppBundle\Doctrine;

use Doctrine\ORM\Mapping\ClassMetadataInfo;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class UuidClassMetadata extends ClassMetadataInfo
{
    /**
     * {@inheritdoc}
     */
    public function getIdentifierValues($entity)
    {
        $this->setIdentifier(['id']);

        return ['id' => $entity->getBytes()];
    }
}
