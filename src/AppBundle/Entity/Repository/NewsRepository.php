<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Doctrine\EntityRepository;
use AppBundle\Entity\News;
use Doctrine\ORM\QueryBuilder;
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
     * @param int  $pageIndex
     * @param bool $publishedOnly
     * @param bool $withInternal
     *
     * @return Pagerfanta
     */
    public function findLatest(int $pageIndex, $publishedOnly = true, $withInternal = false): Pagerfanta
    {
        return $this->paginate($this->queryLatest($publishedOnly, $withInternal))
            ->setMaxPerPage(News::NUM_ITEMS)
            ->setCurrentPage($pageIndex);
    }

    /**
     * @param $publishedOnly
     * @param $withInternal
     *
     * @return QueryBuilder
     */
    private function queryLatest($publishedOnly, $withInternal): QueryBuilder
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

            return $qb;
        }

        return $qb;
    }
}
