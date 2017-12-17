<?php

declare(strict_types=1);

namespace App\Repository;

use App\Doctrine\EntityRepository;
use App\Entity\Purpose;

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

    /**
     * {@inheritdoc}
     */
    protected function getClass(): string
    {
        return Purpose::class;
    }
}
