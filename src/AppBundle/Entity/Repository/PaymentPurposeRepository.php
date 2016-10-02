<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Doctrine\EntityRepository;
use AppBundle\Entity\PaymentPurpose;
use Pagerfanta\Pagerfanta;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class PaymentPurposeRepository extends EntityRepository
{
    /**
     * {@inheritdoc}
     */
    protected function getClass(): string
    {
        return PaymentPurpose::class;
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
}
