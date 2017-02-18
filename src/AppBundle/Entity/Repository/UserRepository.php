<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Doctrine\EntityRepository;
use AppBundle\Entity\User;
use Ramsey\Uuid\UuidInterface;

/**
 * @method User get(UuidInterface $id)
 *
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class UserRepository extends EntityRepository
{
    /**
     * {@inheritdoc}
     */
    protected function getClass(): string
    {
        return User::class;
    }
}
