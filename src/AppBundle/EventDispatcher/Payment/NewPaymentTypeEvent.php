<?php

namespace AppBundle\EventDispatcher\Payment;

use AppBundle\Entity\PaymentType;
use AppBundle\Entity\User;
use AppBundle\Form\Model\PaymentTypeModel;
use Symfony\Component\EventDispatcher\Event;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class NewPaymentTypeEvent extends Event
{
    /**
     * @var PaymentType
     */
    private $paymentType;

    /**
     * @var PaymentTypeModel
     */
    private $paymentTypeModel;

    /**
     * @var User
     */
    private $user;

    /**
     * @param PaymentTypeModel $PaymentTypeModel
     */
    public function __construct(PaymentType $paymentType, PaymentTypeModel $paymentTypeModel, User $user)
    {
        $this->paymentType = $paymentType;
        $this->paymentTypeModel = $paymentTypeModel;
        $this->user = $user;
    }

    /**
     * @return PaymentType
     */
    public function getPaymentType(): PaymentType
    {
        return $this->paymentType;
    }

    /**
     * @return PaymentTypeModel
     */
    public function getPaymentTypeModel(): PaymentTypeModel
    {
        return $this->paymentTypeModel;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }
}
