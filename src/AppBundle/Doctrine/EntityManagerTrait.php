<?php

namespace AppBundle\Doctrine;

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
     * @param EntityManager $em
     */
    public function setEntityManager(EntityManager $em)
    {
        $this->em = $em;
    }
}
