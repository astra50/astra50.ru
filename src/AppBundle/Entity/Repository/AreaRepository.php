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
     * @return array
     */
    public function findPayable()
    {
        return $this->createQueryBuilder('a')
            ->getQuery()
            ->getResult();
    }

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
            ->leftJoin('a.users', 'u')
            ->orderBy('numbers', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param string $displayField
     * @param array  $orderBy
     *
     * @return array
     */
    public function findAllForChoices(string $displayField, array $orderBy = []): array
    {
        return $this->createQueryBuilder('a')
            ->select('a.id', 'ABS(a.number) as number')
            ->orderBy('number', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
