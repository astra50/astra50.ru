<?php

declare(strict_types=1);

namespace App\Entity\Repository;

use App\Doctrine\EntityRepository;
use App\Entity\Area;
use Ramsey\Uuid\UuidInterface;

/**
 * @method Area get(UuidInterface $id)
 *
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class AreaRepository extends EntityRepository
{
    public function findPayable(): array
    {
        return $this->createQueryBuilder('a')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Area[]
     */
    public function findAllWithOwners(): array
    {
        return $this->createQueryBuilder('a')
            ->select('a', 'u', 'ABS(a.number) as HIDDEN numbers')
            ->leftJoin('a.users', 'u')
            ->orderBy('numbers', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findAllForChoices(string $displayField, array $orderBy = []): array
    {
        return $this->createQueryBuilder('a')
            ->select('a.id', 'ABS(a.number) as number')
            ->orderBy('number', 'ASC')
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
}
