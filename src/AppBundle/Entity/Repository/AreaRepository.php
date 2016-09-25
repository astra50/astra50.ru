<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Doctrine\EntityRepository;
use AppBundle\Entity\Area;
use Ramsey\Uuid\UuidInterface;

/**
 * @method Area get(UuidInterface $id)
 *
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class AreaRepository extends EntityRepository
{
    /**
     * {@inheritdoc}
     */
    protected function getClass(): string
    {
        return Area::class;
    }

    /**
     * @return Area[]
     */
    public function findAllWithOwners()
    {
        return $this->createQueryBuilder('a')
            ->select('a', 'u', 'ABS(a.number) as HIDDEN numbers')
            ->join('a.users', 'u')
            ->orderBy('numbers', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return array
     */
    public function findAllForChoices()
    {
        return $this->createQueryBuilder('a')
            ->select('a.id', 'a.number')
            ->getQuery()->getResult()
            ;
    }
}
