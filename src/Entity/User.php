<?php

declare(strict_types=1);

namespace App\Entity;

use App\Doctrine\ORM\Mapping\Traits\Identity;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use Ramsey\Uuid\UuidInterface;

/**
 * @method UuidInterface getId()
 *
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

    /**
     * @param string $phone
     */
    public function setPhone(string $phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * @return string
     */
    public function getPhone(): ?string
    {
        return $this->phone;
    }

    /**
     * @param string $realname
     */
    public function setRealname(string $realname)
    {
        $this->realname = $realname;

        return $this;
    }

    /**
     * @return string
     */
    public function getRealname()
    {
        return $this->realname;
    }
}
