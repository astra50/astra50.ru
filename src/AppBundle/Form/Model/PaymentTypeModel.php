<?php

namespace AppBundle\Form\Model;

use AppBundle\Entity\PaymentType;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class PaymentTypeModel
{
    /**
     * @var string
     *
     * @Assert\NotBlank()
     */
    public $name;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Type("int")
     */
    public $sum;

    /**
     * @var int
     *
     * @Assert\Choice(callback="getSchedules")
     */
    public $schedule;

    /**
     * @var int
     *
     * @Assert\Choice(callback="getCalculations")
     */
    public $calculation;

    /**
     * @var array
     */
    public $areas;

    /**
     * @param PaymentType $entity
     *
     * @return PaymentTypeModel
     */
    public static function fromEntity(PaymentType $entity): PaymentTypeModel
    {
        $model = new static();
        $model->name = $entity->getName();
        $model->sum = $entity->getSum();
        $model->schedule = $entity->getSchedule();

        return $model;
    }

    /**
     * @return array
     */
    public function getSchedules(): array
    {
        return [
            PaymentType::SCHEDULE_ONCE,
            PaymentType::SCHEDULE_MONTHLY,
        ];
    }

    /**
     * @return array
     */
    public function getCalculations(): array
    {
        return [
            PaymentType::CALCULATION_EACH,
            PaymentType::CALCULATION_SIZE,
            PaymentType::CALCULATION_SHARE,
        ];
    }
}
