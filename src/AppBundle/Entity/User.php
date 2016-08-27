<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity
 */
class User extends BaseUser
{
    const SUPPORT_OAUTH_PROVIDERS = [
        'google',
        'vkontakte',
    ];

    /**
     * @var UuidInterface
     *
     * @ORM\Id
     * @ORM\Column(type="uuid_binary")
     */
    protected $id;

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

    /**
     * @param null $id
     * @param      $username
     * @param      $email
     */
    public function __construct($id = null, $username = null, $email = null, $password = null, $enabled = false)
    {
        parent::__construct();

        $this->id = $id ?? Uuid::uuid4();
        $this->username = $username;
        $this->email = $email;
        $this->password = $password;
        $this->enabled = $enabled;
    }

    /**
     * @param $provider
     * @param $id
     * @param $token
     */
    public function updateOauth2($provider, $id, $token)
    {
        if (!in_array($provider, self::SUPPORT_OAUTH_PROVIDERS, true)) {
            throw new \InvalidArgumentException(sprintf('Can\'t update undefined provider "%s"', $provider));
        }

        $this->{sprintf('%sId', $provider)} = $id;
        $this->{sprintf('%sAccessToken', $provider)} = $token;
    }
}
