<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Doctrine\EntityRepository;
use AppBundle\Entity\Street;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class StreetRepository extends EntityRepository
{
    /**
     * {@inheritdoc}
     */
    protected function getClass(): string
    {
        return Street::class;
    }

    /**
     * @return Street[]
     */
    public function findAll()
    {
        return $this->createQueryBuilder('s')
            ->getQuery()->getResult();
    }
}
