<?php

namespace AppBundle\EventDispatcher\Payment;

use AppBundle\Entity\PaymentPurpose;
use AppBundle\Entity\User;
use AppBundle\Form\Model\PaymentPurposeModel;
use Symfony\Component\EventDispatcher\Event;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class PaymentPurposeEvent extends Event
{
    /**
     * @var PaymentPurpose
     */
    private $paymentPurpose;

    /**
     * @var PaymentPurposeModel
     */
    private $paymentPurposeModel;

    /**
     * @var User
     */
    private $user;

    /**
     * @param PaymentPurposeModel $PaymentPurposeModel
     */
    public function __construct(PaymentPurpose $paymentPurpose, PaymentPurposeModel $paymentPurposeModel, User $user)
    {
        $this->paymentPurpose = $paymentPurpose;
        $this->paymentPurposeModel = $paymentPurposeModel;
        $this->user = $user;
    }

    /**
     * @return PaymentPurpose
     */
    public function getPaymentPurpose(): PaymentPurpose
    {
        return $this->paymentPurpose;
    }

    /**
     * @return PaymentPurposeModel
     */
    public function getPaymentPurposeModel(): PaymentPurposeModel
    {
        return $this->paymentPurposeModel;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }
}
