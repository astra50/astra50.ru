<?php

declare(strict_types=1);

namespace App\Form\Model;

use App\Entity\Area;
use App\Entity\Purpose;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class PurposeModel
{
    /**
     * @var string
     *
     * @Assert\NotBlank
     */
    public $name;

    /**
     * @var int
     *
     * @Assert\NotBlank
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
     *     @Assert\Type("App\Entity\Area")
     * })
     */
    public $areas;

    /**
     * @param Purpose $entity
     *
     * @return PurposeModel
     */
    public static function fromEntity(Purpose $entity): self
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
