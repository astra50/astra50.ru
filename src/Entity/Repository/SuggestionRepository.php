<?php

declare(strict_types=1);

namespace App\Entity\Repository;

use App\Doctrine\EntityRepository;
use App\Entity\Suggestion;
use Pagerfanta\Pagerfanta;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class SuggestionRepository extends EntityRepository
{
    public function findLatest(int $page): Pagerfanta
    {
        $query = $this->createQueryBuilder('e')
            ->orderBy('e.createdAt', 'DESC')
            ->getQuery();

        return $this->createPaginator($query, constant($this->getClass().'::NUM_ITEMS'), $page);
    }

    /**
     * {@inheritdoc}
     */
    protected function getClass(): string
    {
        return Suggestion::class;
    }
}
