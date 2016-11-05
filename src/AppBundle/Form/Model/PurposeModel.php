<?php

namespace AppBundle\Form\Model;

use AppBundle\Entity\Area;
use AppBundle\Entity\Purpose;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class PurposeModel
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
    public $amount;

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
     * @var Area[]
     *
     * @Assert\All({
     *      @Assert\Type("AppBundle\Entity\Area")
     * })
     */
    public $areas;

    /**
     * @param Purpose $entity
     *
     * @return PurposeModel
     */
    public static function fromEntity(Purpose $entity): PurposeModel
    {
        $model = new static();
        $model->name = $entity->getName();
        $model->amount = $entity->getAmount();
        $model->schedule = $entity->getSchedule();

        return $model;
    }

    /**
     * @return array
     */
    public function getSchedules(): array
    {
        return [
            Purpose::SCHEDULE_ONCE,
            Purpose::SCHEDULE_MONTHLY,
        ];
    }

    /**
     * @return array
     */
    public function getCalculations(): array
    {
        return [
            Purpose::CALCULATION_EACH,
            Purpose::CALCULATION_SIZE,
            Purpose::CALCULATION_SHARE,
        ];
    }
}
