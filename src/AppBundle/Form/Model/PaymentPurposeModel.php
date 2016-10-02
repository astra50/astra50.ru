<?php

namespace AppBundle\Form\Model;

use AppBundle\Entity\PaymentPurpose;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class PaymentPurposeModel
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
     * @param PaymentPurpose $entity
     *
     * @return PaymentPurposeModel
     */
    public static function fromEntity(PaymentPurpose $entity): PaymentPurposeModel
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
            PaymentPurpose::SCHEDULE_ONCE,
            PaymentPurpose::SCHEDULE_MONTHLY,
        ];
    }

    /**
     * @return array
     */
    public function getCalculations(): array
    {
        return [
            PaymentPurpose::CALCULATION_EACH,
            PaymentPurpose::CALCULATION_SIZE,
            PaymentPurpose::CALCULATION_SHARE,
        ];
    }
}
