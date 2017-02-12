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
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator("\Ramsey\Uuid\Doctrine\UuidGenerator")
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
     * @var Street
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Street")
     * @ORM\JoinColumn()
     */
    private $street;

    /**
     * @var User[]
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinTable(joinColumns={@ORM\JoinColumn()}, inverseJoinColumns={@ORM\JoinColumn()})
     */
    private $users;

    public function __construct(string $number, int $size, Street $street = null)
    {
        $this->number = $number;
        $this->size = $size;
        $this->street = $street;
        $this->users = new ArrayCollection();
    }

    public function setSize(int $size)
    {
        $this->size = $size;
    }

    public function setStreet(Street $street = null)
    {
        $this->street = $street;
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

    /**
     * @return Street
     */
    public function getStreet()
    {
        return $this->street;
    }

    public function replaceUsers($users)
    {
        if (!is_array($users) && !$users instanceof \Traversable) {
            throw new \InvalidArgumentException('Users must be iterable type');
        }

        $this->users->clear();

        foreach ($users as $user) {
            $this->addUser($user);
        }
    }

    private function addUser(User $user)
    {
        if ($this->users->contains($user)) {
            return;
        }

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
