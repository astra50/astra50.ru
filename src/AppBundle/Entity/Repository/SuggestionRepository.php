<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Doctrine\EntityRepository;
use AppBundle\Entity\Suggestion;

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
