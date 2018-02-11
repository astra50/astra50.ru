<?php

declare(strict_types=1);

namespace App\Entity;

use App\Doctrine\ORM\Mapping\Traits\Identity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;

/**
 * @ORM\Entity
 * @ORM\Table(name="users")
 */
class User extends BaseUser
{
    use Identity;

    public const SUPPORT_OAUTH_PROVIDERS = [
        'google',
        'vkontakte',
    ];

    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(nullable=true)
     */
    private $phone;

    /**
     * @var string
     *
     * @ORM\Column
     */
    private $realname;

    /**
     * @var Area[]
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\Area", mappedBy="users")
     */
    private $areas;

    /**
     * @ORM\Column(nullable=true)
     */
    private $googleId;

    /**
     * @ORM\Column(nullable=true)
     */
    private $googleAccessToken;

    /**
     * @ORM\Column(nullable=true)
     */
    private $vkontakteId;

    /**
     * @ORM\Column(nullable=true)
     */
    private $vkontakteAccessToken;

    public function __construct($email = null, $realname = null, $password = null, $enabled = false)
    {
        parent::__construct();

        $this->username = $email;
        $this->email = $email;
        $this->realname = $realname;
        $this->password = $password;
        $this->enabled = $enabled;
        $this->areas = new ArrayCollection();
    }

    public function updateOauth2(string $provider, $id, ?string $token): void
    {
        if (!in_array($provider, self::SUPPORT_OAUTH_PROVIDERS, true)) {
            throw new \InvalidArgumentException(sprintf('Can\'t update undefined provider "%s"', $provider));
        }

        $this->{sprintf('%sId', $provider)} = (string) $id;
        $this->{sprintf('%sAccessToken', $provider)} = $token;
    }

    public function setEmail($email)
    {
        $this->username = $email;

        return parent::setEmail($email);
    }

    public function setUsername($username): void
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

    public function getRealname(): string
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
}
