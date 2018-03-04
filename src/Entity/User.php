<?php

declare(strict_types=1);

namespace App\Entity;

use App\Doctrine\ORM\Mapping\Traits\CreatedAt;
use App\Doctrine\ORM\Mapping\Traits\Identity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Serializable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="users")
 *
 * @UniqueEntity(fields={"username"})
 */
class User implements UserInterface, EquatableInterface, Serializable
{
    use Identity;
    use CreatedAt;

    private const PASSWORD_CREDENTIALS_TYPE = 'password';

    /**
     * @var string
     *
     * @Assert\Email
     * @Assert\NotBlank
     *
     * @ORM\Column(length=180, unique=true)
     */
    protected $username;

    /**
     * @var array
     *
     * @ORM\Column(type="array")
     */
    protected $roles = [];

    /**
     * @var Credential[]|ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Credential", mappedBy="user", cascade={"persist", "remove"})
     */
    private $credentials;

    /**
     * @var string
     *
     * @ORM\Column
     */
    private $realname;

    /**
     * @var string
     *
     * @ORM\Column(nullable=true)
     */
    private $phone;

    /**
     * @var Area[]|ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\Area", mappedBy="users")
     */
    private $areas;

    public function __construct()
    {
        $this->credentials = new ArrayCollection();
        $this->areas = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function addRole(string $role): void
    {
        if (in_array($role, $this->roles, true)) {
            return;
        }

        $this->roles[] = $role;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return $roles;
    }

    public function changePassword(string $password, PasswordEncoderInterface $encoder): void
    {
        $this->authenticate(self::PASSWORD_CREDENTIALS_TYPE, $encoder->encodePassword($password, ''));
    }

    public function authenticate(string $type, string $identifier, array $payloads = []): void
    {
        if ($credential = $this->getCredential($type)) {
            $credential->expire();
        }

        $this->credentials[] = new Credential($this, $type, $identifier, $payloads);
    }

    public function isValidCredential(string $type, string $identifier, array $payloads = []): bool
    {
        if (!$credential = $this->getCredential($type)) {
            return false;
        }

        if ($credential->getIdentifier() !== $identifier) {
            return false;
        }

        if ($payloads && $payloads !== array_intersect_key($credential->getPayloads(), $payloads)) {
            return false;
        }

        return true;
    }

    public function isAuthenticatedWith(string $type): bool
    {
        if (!$credential = $this->getCredential($type)) {
            return false;
        }

        if (null === $credential->getExpiredAt()) {
            return true;
        }

        return false;
    }

    public function revokeCredential(string $type): void
    {
        if (!$credential = $this->getCredential($type)) {
            return;
        }

        $credential->expire();
    }

    /**
     * {@inheritdoc}
     */
    public function getPassword(): ?string
    {
        $credential = $this->getCredential(self::PASSWORD_CREDENTIALS_TYPE);

        return $credential ? $credential->getIdentifier() : null;
    }

    /**
     * {@inheritdoc}
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function eraseCredentials(): void
    {
    }

    public function setPhone(string $phone)
    {
        $this->phone = $phone;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setRealname(string $realname)
    {
        $this->realname = $realname;

        return $this;
    }

    public function getRealname(): ?string
    {
        return $this->realname;
    }

    /**
     * @return Area[]
     */
    public function getAreas(): array
    {
        return $this->areas->toArray();
    }

    public function isEqualTo(UserInterface $user): bool
    {
        if (!$user instanceof self) {
            return false;
        }

        if ($user->getUsername() !== $this->username) {
            return false;
        }

        if ($user->getRoles() !== $this->getRoles()) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function serialize(): string
    {
        return serialize([
            $this->id,
            $this->username,
            $this->roles,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized): void
    {
        [
            $this->id,
            $this->username,
            $this->roles
        ] = unserialize($serialized, ['allowed_classes' => false]);
    }

    private function getCredential(string $type): ?Credential
    {
        $criteria = Criteria::create()
            ->andWhere(Criteria::expr()->eq('type', $type))
            ->andWhere(Criteria::expr()->isNull('expiredAt'));

        $collection = $this->credentials->matching($criteria);

        if ($collection->isEmpty()) {
            return null;
        }

        return $collection->first();
    }
}
