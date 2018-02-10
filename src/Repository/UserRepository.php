<?php

declare(strict_types=1);

namespace App\Repository;

use App\Doctrine\EntityRepository;
use App\Entity\User;
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
