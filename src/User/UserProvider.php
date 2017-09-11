<?php

declare(strict_types=1);

namespace App\User;

use App\Entity\User;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\FOSUBUserProvider;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class UserProvider extends FOSUBUserProvider
{
    /**
     * {@inheritdoc}
     */
    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {
        $username = $response->getUsername();
        $email = $response->getEmail();

        $user = $this->userManager->findUserBy([$this->getProperty($response) => $username]);

        if (!$user) {
            $user = $this->userManager->findUserByEmail($email);
        }

        if (!$user) {
            $user = new User($email, $response->getRealName(), uniqid(null, true), true);
        }

        $user->updateOauth2($response->getResourceOwner()->getName(), $username, $response->getAccessToken());
        $this->userManager->updateUser($user);

        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function connect(UserInterface $user, UserResponseInterface $response): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Expected an instance of FOS\UserBundle\Model\User, but got "%s".', get_class($user)));
        }

        $property = $this->getProperty($response);
        $username = $response->getUsername();

        if (null !== $previousUser = $this->userManager->findUserBy([$property => $username])) {
            $this->disconnect($previousUser, $response);
        }

        $user->updateOauth2($response->getResourceOwner()->getName(), $username, $response->getAccessToken());

        $this->userManager->updateUser($user);
    }

    /**
     * @param User                  $user
     * @param UserResponseInterface $response
     */
    public function disconnect(UserInterface $user, UserResponseInterface $response): void
    {
        $user->updateOauth2($response->getResourceOwner()->getName(), null, null);

        $this->userManager->updateUser($user);
    }
}
