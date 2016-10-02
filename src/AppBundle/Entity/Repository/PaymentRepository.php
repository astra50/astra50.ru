<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Doctrine\EntityRepository;
use AppBundle\Entity\Area;
use AppBundle\Entity\Payment;
use AppBundle\Entity\Purpose;
use Doctrine\ORM\Query\Expr\Join;
use Pagerfanta\Pagerfanta;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class PaymentRepository extends EntityRepository
{
    /**
     * {@inheritdoc}
     */
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
    public function paginatePurposesByArea(Area $area, int $pageSize, int $pageIndex)
    {
        $qb = $this->em->createQueryBuilder()
            ->select('purpose')
            ->addSelect('SUM(payment2.amount) AS amount')
            ->from(Purpose::class, 'purpose')
            ->join(Payment::class, 'payment', Join::WITH, 'payment.purpose = purpose')
            ->leftJoin(Payment::class, 'payment2', Join::WITH, 'payment2.purpose = purpose')
            ->where('payment.area = :area')
            ->setParameter('area', $area)
            ->groupBy('purpose')
            ->orderBy('purpose.id', 'DESC');

        return $this->paginate($qb, $pageSize, $pageIndex);
    }
}
