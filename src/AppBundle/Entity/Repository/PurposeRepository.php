<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Doctrine\EntityRepository;
use AppBundle\Entity\Purpose;
use Pagerfanta\Pagerfanta;

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
     * @param int $pageSize
     * @param int $pageIndex
     *
     * @return Pagerfanta
     */
    public function paginateLatest(int $pageSize, int $pageIndex)
    {
        $qb = $this->createQueryBuilder('pt')
            ->orderBy('pt.id', 'DESC');

        return $this->paginate($qb, $pageSize, $pageIndex);
    }

    /**
     * @return array
     */
    public function findActiveForChoices()
    {
        return $this->createQueryBuilder('pt')
            ->select('pt.id', 'pt.name')
            ->where('pt.archivedAt IS NULL')
            ->orderBy('pt.id', 'DESC')
            ->getQuery()->getResult();
    }
}
