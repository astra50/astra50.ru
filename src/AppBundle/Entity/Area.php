<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity()
 */
class Area
{
    /**
     * @var UuidInterface
     *
     * @ORM\Id()
     * @ORM\Column(type="uuid_binary")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", unique=true)
     */
    private $number;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $size;

    /**
     * @var User[]
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinTable(joinColumns={@ORM\JoinColumn()}, inverseJoinColumns={@ORM\JoinColumn()})
     */
    private $users;

    /**
     * @param UuidInterface $id
     * @param string        $number
     * @param int           $size
     */
    public function __construct(UuidInterface $id, string $number, int $size)
    {
        $this->id = $id;
        $this->number = $number;
        $this->size = $size;
        $this->users = new ArrayCollection();
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getNumber(): string
    {
        return $this->number;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function addUser(User $user)
    {
        $this->users[] = $user;
    }

    /**
     * @return User[]
     */
    public function getUsers(): array
    {
        return $this->users->toArray();
    }
}
