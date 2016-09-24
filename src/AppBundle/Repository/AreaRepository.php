<?php

namespace AppBundle\Repository;

use AppBundle\Doctrine\EntityRepository;
use AppBundle\Entity\Area;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class AreaRepository extends EntityRepository
{
    protected function getClass(): string
    {
        return Area::class;
    }

    /**
     * @return Area[]
     */
    public function findAll()
    {
        return $this->createQueryBuilder('a')
            ->select('a', 'u', 'ABS(a.number) as HIDDEN numbers')
            ->join('a.users', 'u')
            ->orderBy('numbers', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
