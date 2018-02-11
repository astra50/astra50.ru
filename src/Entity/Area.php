<?php

declare(strict_types=1);

namespace App\Entity;

use App\Doctrine\ORM\Mapping\Traits\Identity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Area
{
    use Identity;

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
     * @ORM\ManyToOne(targetEntity="App\Entity\Street")
     * @ORM\JoinColumn
     */
    private $street;

    /**
     * @var User[]|ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\User")
     * @ORM\JoinTable(joinColumns={@ORM\JoinColumn}, inverseJoinColumns={@ORM\JoinColumn})
     */
    private $users;

    public function __construct(string $number, int $size, Street $street = null)
    {
        $this->number = $number;
        $this->size = $size;
        $this->street = $street;
        $this->users = new ArrayCollection();
    }

    public function setSize(int $size): void
    {
        $this->size = $size;
    }

    public function setStreet(Street $street = null): void
    {
        $this->street = $street;
    }

    public function getNumber(): string
    {
        return $this->number;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function getStreet(): ?Street
    {
        return $this->street;
    }

    public function replaceUsers($users): void
    {
        if (!is_array($users) && !$users instanceof \Traversable) {
            throw new \InvalidArgumentException('Users must be iterable type');
        }

        $this->users->clear();

        foreach ($users as $user) {
            $this->addUser($user);
        }
    }

    /**
     * @return User[]
     */
    public function getUsers(): array
    {
        return $this->users->toArray();
    }

    private function addUser(User $user): void
    {
        if ($this->users->contains($user)) {
            return;
        }

        $this->users[] = $user;
    }

    public function isOwner(User $user): bool
    {
        return in_array($user, $this->users->toArray(), true);
    }
}
