<?php

declare(strict_types=1);

namespace App\Entity;

use App\Doctrine\ORM\Mapping\Traits\CreatedAt;
use App\Doctrine\ORM\Mapping\Traits\Identity;
use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Purpose
{
    use Identity;
    use CreatedAt;
    public const NUM_ITEMS = 20;

    public const SCHEDULE_ONCE = 1;
    public const SCHEDULE_MONTHLY = 2;

    public const CALCULATION_AREA = 1;
    public const CALCULATION_SIZE = 2;
    public const CALCULATION_SHARE = 3;

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
     * @var int
     *
     * @ORM\Column(type="smallint")
     */
    private $schedule;

    /**
     * @var int
     *
     * @ORM\Column(type="smallint")
     */
    private $calculation;

    /**
     * @var DateTimeImmutable
     *
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $archivedAt;

    public function __construct(string $name, int $amount, int $schedule, int $calculation)
    {
        $this->name = $name;
        $this->amount = $amount;
        $this->schedule = $schedule;
        $this->calculation = $calculation;
    }

    public function archive(): void
    {
        $this->archivedAt = new DateTimeImmutable();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function getSchedule(): int
    {
        return $this->schedule;
    }

    public function getCalculation(): int
    {
        return $this->calculation;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function getArchivedAt(): ?DateTimeImmutable
    {
        return $this->archivedAt;
    }
}
