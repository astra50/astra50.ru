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
     * @Assert\Type("Ramsey\Uuid\UuidInterface")
     */
    public $purpose;

    /**
     * @var UuidInterface
     *
     * @Assert\NotBlank()
     * @Assert\Type("Ramsey\Uuid\UuidInterface")
     */
    public $area;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Type("int")
     * @Assert\GreaterThan(0)
     */
    public $amount;

    /**
     * @var bool
     *
     * @Assert\NotNull()
     * @Assert\Type("bool")
     */
    public $isPositive;

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
        $model->amount = abs($entity->getAmount());
        $model->isPositive = $entity->getAmount() > 0;

        return $model;
    }
}
