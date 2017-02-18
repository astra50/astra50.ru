<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity
 */
class Purpose
{
    const NUM_ITEMS = 20;

    const SCHEDULE_ONCE = 1;
    const SCHEDULE_MONTHLY = 2;

    const CALCULATION_EACH = 1;
    const CALCULATION_SIZE = 2;
    const CALCULATION_SHARE = 3;

    /**
     * @var UuidInterface
     *
     * @ORM\Id
     * @ORM\Column(type="uuid_binary")
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator("\AppBundle\Doctrine\UuidGenerator")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column()
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
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $archivedAt;

    public function __construct(string $name, int $amount, int $schedule, int $calculation)
    {
        $this->name = $name;
        $this->amount = $amount;
        $this->schedule = $schedule;
        $this->calculation = $calculation;
        $this->createdAt = new \DateTime();
    }

    public function archive()
    {
        $this->archivedAt = new \DateTime();
    }

    /**
     * @return UuidInterface
     */
    public function getId(): UuidInterface
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getAmount(): int
    {
        return $this->amount;
    }

    /**
     * @return int
     */
    public function getSchedule(): int
    {
        return $this->schedule;
    }

    /**
     * @return int
     */
    public function getCalculation(): int
    {
        return $this->calculation;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    /**
     * @return \DateTime
     */
    public function getArchivedAt()
    {
        return $this->archivedAt;
    }
}
