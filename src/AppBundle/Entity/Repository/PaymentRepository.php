<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Doctrine\EntityRepository;
use AppBundle\Entity\Area;
use AppBundle\Entity\Payment;
use Pagerfanta\Pagerfanta;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class PaymentRepository extends EntityRepository
{
    protected function getClass(): string
    {
        return Payment::class;
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
     * @param Area $area
     * @param int  $pageSize
     * @param int  $pageIndex
     *
     * @return Pagerfanta
     */
    public function paginateByArea(Area $area, int $pageSize, int $pageIndex)
    {
        $qb = $this->createQueryBuilder('p')
            ->where('p.area = :area')
            ->setParameter('area', $area)
            ->orderBy('p.createdAt', 'DESC');

        return $this->paginate($qb, $pageSize, $pageIndex);
    }
}
