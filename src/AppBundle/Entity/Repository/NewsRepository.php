<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Doctrine\EntityRepository;
use AppBundle\Entity\News;
use AppBundle\Entity\User;
use Pagerfanta\Pagerfanta;
use Ramsey\Uuid\UuidInterface;

/**
 * @method News create(UuidInterface $id, User $author, string $title, string $content, bool $published, bool $internal)
 *
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
     * @param int  $pageSize
     * @param int  $pageIndex
     * @param bool $publishedOnly
     * @param bool $withInternal
     *
     * @return Pagerfanta
     */
    public function paginateLatest(int $pageSize, int $pageIndex, $publishedOnly = true, $withInternal = false)
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

        return $this->paginate($qb, $pageSize, $pageIndex);
    }
}
