<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Doctrine\EntityRepository;
use AppBundle\Entity\News;
use Pagerfanta\Pagerfanta;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class NewsRepository extends EntityRepository
{
    /**
     * {@inheritdoc}
     */
    protected function getClass(): string
    {
        return News::class;
    }

    /**
     * @param int  $page
     * @param bool $publishedOnly
     * @param bool $withInternal
     *
     * @return Pagerfanta
     */
    public function findLatest(int $page, $publishedOnly = true, $withInternal = false): Pagerfanta
    {
        $qb = $this->createQueryBuilder('n')
            ->addSelect('u')
            ->join('n.author', 'u');

        if ($publishedOnly) {
            $qb->where('n.published = :published')
                ->setParameter('published', true);
        }

        if (!$withInternal) {
            $qb->andWhere('n.internal = :internal')
                ->setParameter('internal', false);
        }

        return $this->createPaginator($qb->getQuery(), News::NUM_ITEMS, $page);
    }
}
