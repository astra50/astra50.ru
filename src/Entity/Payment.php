<?php

declare(strict_types=1);

namespace App\Entity;

use App\Doctrine\ORM\Mapping\Traits\CreatedAt;
use App\Doctrine\ORM\Mapping\Traits\Identity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Payment
{
    use Identity;
    use CreatedAt;
    public const NUM_ITEMS = 50;

    /**
     * @var Area
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Area")
     * @ORM\JoinColumn
     */
    private $area;

    /**
     * @var Purpose
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Purpose")
     * @ORM\JoinColumn
     */
    private $purpose;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn
     */
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $comment;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $amount;

    public function __construct(Area $area, Purpose $purpose, User $user, $amount)
    {
        $this->area = $area;
        $this->purpose = $purpose;
        $this->user = $user;
        $this->amount = $amount;
    }

    /**
     * @param string $comment
     */
    public function setComment(string $comment): void
    {
        $this->comment = $comment;
    }

    /**
     * @return Area
     */
    public function getArea(): Area
    {
        return $this->area;
    }

    /**
     * @return Purpose
     */
    public function getPurpose(): Purpose
    {
        return $this->purpose;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @return string
     */
    public function getComment(): string
    {
        return $this->comment;
    }

    /**
     * @return int
     */
    public function getAmount(): int
    {
        return $this->amount;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }
}
