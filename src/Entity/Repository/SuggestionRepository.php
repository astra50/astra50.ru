<?php

declare(strict_types=1);

namespace App\Entity\Repository;

use App\Doctrine\EntityRepository;
use App\Entity\Suggestion;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class SuggestionRepository extends EntityRepository
{
    /**
     * {@inheritdoc}
     */
    protected function getClass(): string
    {
        return Suggestion::class;
    }
}
