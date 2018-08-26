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

    public function __construct(Area $area, Purpose $purpose, User $user, int $amount, string $comment = null)
    {
        $this->area = $area;
        $this->purpose = $purpose;
        $this->user = $user;
        $this->amount = $amount;
        $this->comment = $comment;
    }

    public function getArea(): Area
    {
        return $this->area;
    }

    public function getPurpose(): Purpose
    {
        return $this->purpose;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }
}
