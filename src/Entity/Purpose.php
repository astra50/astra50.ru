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
use DomainException;
use Symfony\Component\Validator\Constraints as Assert;

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
     * @Assert\NotBlank
     *
     * @ORM\Column
     */
    private $name;

    /**
     * @var int
     *
     * @Assert\NotBlank
     *
     * @ORM\Column(type="integer")
     */
    private $amount;

    /**
     * @var Schedule
     *
     * @Assert\Type("App\Entity\Enum\Schedule")
     * @Assert\NotBlank
     *
     * @ORM\Column(type="schedule_enum")
     */
    private $schedule;

    /**
     * @var Calculation
     *
     * @Assert\Type("App\Entity\Enum\Calculation")
     * @Assert\NotBlank
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
        $areas = $this->areas->toArray();

        usort($areas, function (Area $lft, Area $rgt) {
            return $lft->getNumber() >= $rgt->getNumber();
        });

        return $areas;
    }

    public function archive(): void
    {
        if (null !== $this->archivedAt) {
            throw new DomainException('Purpose already archived');
        }

        $this->archivedAt = new DateTimeImmutable();
    }

    public function getArchivedAt(): ?DateTimeImmutable
    {
        return $this->archivedAt;
    }

    public function isEditable(): bool
    {
        return null === $this->getArchivedAt();
    }

    public function isPayable(): bool
    {
        return null === $this->getArchivedAt();
    }

    public function isArchived(): bool
    {
        return null !== $this->getArchivedAt();
    }
}
