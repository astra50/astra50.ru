<?php

declare(strict_types=1);

namespace App\Doctrine;

use Doctrine\ORM\EntityManager;

/**
 * @author Konstantin Grachev <ko@grachev.io>
 */
trait EntityManagerTrait
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @required
     */
    public function setEntityManager(EntityManager $em): void
    {
        $this->em = $em;
    }
}
