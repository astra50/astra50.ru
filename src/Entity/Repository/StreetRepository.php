<?php

declare(strict_types=1);

namespace App\Entity\Repository;

use App\Doctrine\EntityRepository;
use App\Entity\Street;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class StreetRepository extends EntityRepository
{
    /**
     * @return Street[]
     */
    public function findAll()
    {
        return $this->createQueryBuilder('s')
            ->getQuery()->getResult();
    }

    /**
     * {@inheritdoc}
     */
    protected function getClass(): string
    {
        return Street::class;
    }
}
