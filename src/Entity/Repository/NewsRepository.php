<?php

declare(strict_types=1);

namespace App\Entity\Repository;

use App\Doctrine\EntityRepository;
use App\Entity\News;
use Pagerfanta\Pagerfanta;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class NewsRepository extends EntityRepository
{
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

    /**
     * {@inheritdoc}
     */
    protected function getClass(): string
    {
        return News::class;
    }
}
