<?php

namespace AppBundle\EventDispatcher\Payment;

use AppBundle\Entity\Purpose;
use AppBundle\Entity\User;
use AppBundle\Form\Model\PurposeModel;
use Symfony\Component\EventDispatcher\Event;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class PurposeEvent extends Event
{
    /**
     * @var Purpose
     */
    private $purpose;

    /**
     * @var PurposeModel
     */
    private $purposeModel;

    /**
     * @var User
     */
    private $user;

    /**
     * @param PurposeModel $PurposeModel
     */
    public function __construct(Purpose $purpose, PurposeModel $purposeModel, User $user)
    {
        $this->purpose = $purpose;
        $this->purposeModel = $purposeModel;
        $this->user = $user;
    }

    /**
     * @return Purpose
     */
    public function getPurpose(): Purpose
    {
        return $this->purpose;
    }

    /**
     * @return PurposeModel
     */
    public function getPurposeModel(): PurposeModel
    {
        return $this->purposeModel;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }
}
