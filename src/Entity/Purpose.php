<?php

declare(strict_types=1);

namespace App\Entity;

use App\Doctrine\ORM\Mapping\Traits\CreatedAt;
use App\Doctrine\ORM\Mapping\Traits\Identity;
use App\Entity\Enum\Calculation;
use App\Entity\Enum\Schedule;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Purpose
{
    use Identity;
    use CreatedAt;

    /**
     * @var string
     *
     * @ORM\Column
     */
    private $name;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $amount;

    /**
     * @var Schedule
     *
     * @ORM\Column(type="schedule_enum")
     */
    private $schedule;

    /**
     * @var Calculation
     *
     * @ORM\Column(type="calculation_enum")
     */
    private $calculation;

    /**
     * @var Area[]|ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\Area")
     */
    private $areas;

    /**
     * @var DateTimeImmutable
     *
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $archivedAt;

    public function __construct()
    {
        $this->schedule = Schedule::once();
        $this->calculation = Calculation::area();
        $this->areas = new ArrayCollection();
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): void
    {
        $this->amount = $amount;
    }

    public function getSchedule(): Schedule
    {
        return $this->schedule;
    }

    public function setSchedule(Schedule $schedule): void
    {
        $this->schedule = $schedule;
    }

    public function getCalculation(): Calculation
    {
        return $this->calculation;
    }

    public function setCalculation(Calculation $calculation): void
    {
        $this->calculation = $calculation;
    }

    public function setAreas(?array $areas): void
    {
        $this->areas->clear();

        if (null === $areas) {
            return;
        }

        foreach ($areas as $area) {
            $this->areas[] = $area;
        }
    }

    /**
     * @return Area[]
     */
    public function getAreas(): array
    {
        return $this->areas->toArray();
    }

    public function archive(): void
    {
        $this->archivedAt = new DateTimeImmutable();
    }

    public function getArchivedAt(): ?DateTimeImmutable
    {
        return $this->archivedAt;
    }
}
