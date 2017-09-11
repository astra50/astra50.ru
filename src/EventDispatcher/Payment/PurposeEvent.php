<?php

declare(strict_types=1);

namespace App\EventDispatcher\Payment;

use App\Entity\Purpose;
use App\Entity\User;
use App\Form\Model\PurposeModel;
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
