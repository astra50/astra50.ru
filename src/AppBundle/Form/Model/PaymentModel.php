<?php

namespace AppBundle\Form\Model;

use AppBundle\Entity\Payment;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class PaymentModel
{
    /**
     * @var UuidInterface
     *
     * @Assert\NotBlank()
     * @Assert\Uuid()
     */
    public $purpose;

    /**
     * @var UuidInterface
     *
     * @Assert\NotBlank()
     * @Assert\Uuid()
     */
    public $area;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Type("int")
     */
    public $amount;

    /**
     * @param Payment $entity
     *
     * @return PaymentModel
     */
    public static function fromEntity(Payment $entity): PaymentModel
    {
        $model = new static();
        $model->area = $entity->getArea();
        $model->purpose = $entity->getPurpose();
        $model->amount = $entity->getAmount();

        return $model;
    }
}
