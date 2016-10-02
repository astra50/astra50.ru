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
     * @var PaymentPurpose
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\PaymentPurpose")
     * @ORM\JoinColumn()
     */
    private $paymentPurpose;

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
    private $sum;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @param UuidInterface $id
     * @param Area          $area
     * @param PaymentPurpose   $paymentPurpose
     * @param User          $user
     * @param int           $sum
     */
    public function __construct(UuidInterface $id, Area $area, PaymentPurpose $paymentPurpose, User $user, $sum)
    {
        $this->id = $id;
        $this->area = $area;
        $this->paymentPurpose = $paymentPurpose;
        $this->user = $user;
        $this->sum = $sum;
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
     * @return PaymentPurpose
     */
    public function getPaymentPurpose(): PaymentPurpose
    {
        return $this->paymentPurpose;
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
    public function getSum(): int
    {
        return $this->sum;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }
}
