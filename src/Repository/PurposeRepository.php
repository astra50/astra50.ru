<?php

declare(strict_types=1);

namespace App\Repository;

use App\Doctrine\EntityRepository;
use App\Entity\Purpose;
use Pagerfanta\Pagerfanta;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class PurposeRepository extends EntityRepository
{
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

    public function findLatest(int $page): Pagerfanta
    {
        $query = $this->createQueryBuilder('e')
            ->orderBy('e.createdAt', 'DESC')
            ->getQuery();

        return $this->createPaginator($query, constant($this->getClass().'::NUM_ITEMS'), $page);
    }

    /**
     * {@inheritdoc}
     */
    protected function getClass(): string
    {
        return Purpose::class;
    }
}
