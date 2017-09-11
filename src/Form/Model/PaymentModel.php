<?php

declare(strict_types=1);

namespace App\Form\Model;

use App\Entity\Area;
use App\Entity\Payment;
use App\Entity\Purpose;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class PaymentModel
{
    /**
     * @var Purpose
     *
     * @Assert\NotBlank
     * @Assert\Type("App\Entity\Purpose")
     */
    public $purpose;

    /**
     * @var Area
     *
     * @Assert\NotBlank
     * @Assert\Type("App\Entity\Area")
     */
    public $area;

    /**
     * @var string
     *
     * @Assert\NotBlank
     * @Assert\Type("int")
     * @Assert\GreaterThan(0)
     */
    public $amount;

    /**
     * @var bool
     *
     * @Assert\NotNull
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