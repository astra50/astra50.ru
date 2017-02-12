<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Doctrine\EntityRepository;
use AppBundle\Entity\Purpose;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class PurposeRepository extends EntityRepository
{
    /**
     * {@inheritdoc}
     */
    protected function getClass(): string
    {
        return Purpose::class;
    }

    /**
     * @return array
     */
    public function findActive()
    {
        return $this->createQueryBuilder('pt')
            ->where('pt.archivedAt IS NULL')
            ->orderBy('pt.id', 'DESC')
            ->getQuery()->getResult();
    }
}
