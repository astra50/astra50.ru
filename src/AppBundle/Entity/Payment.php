<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity
 */
class Payment
{
    /**
     * @var UuidInterface
     *
     * @ORM\Id
     * @ORM\Column(type="uuid_binary")
     */
    private $id;

    /**
     * @var Area
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Area")
     * @ORM\JoinColumn()
     */
    private $area;

    /**
     * @var Purpose
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Purpose")
     * @ORM\JoinColumn()
     */
    private $purpose;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn()
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

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @param UuidInterface $id
     * @param Area          $area
     * @param Purpose       $purpose
     * @param User          $user
     * @param int           $amount
     */
    public function __construct(UuidInterface $id, Area $area, Purpose $purpose, User $user, $amount)
    {
        $this->id = $id;
        $this->area = $area;
        $this->purpose = $purpose;
        $this->user = $user;
        $this->amount = $amount;
        $this->createdAt = new \DateTime();
    }

    /**
     * @param string $comment
     */
    public function setComment(string $comment)
    {
        $this->comment = $comment;
    }

    /**
     * @return UuidInterface
     */
    public function getId(): UuidInterface
    {
        return $this->id;
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
