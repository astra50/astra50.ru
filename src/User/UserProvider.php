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
    public function loadUserByOAuthUserResponse(UserResponseInterface $response): UserInterface
    {
        $username = $response->getUsername();
        $email = $response->getEmail();

        $user = $this->userManager->findUserBy([$this->getProperty($response) => $username]);

        if (!$user) {
            $user = $this->userManager->findUserByEmail($email);
        }

        if (!$user) {
            $user = new User($email, $response->getRealName(), uniqid('', true), true);
        }

        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('$user must be instance of %s', User::class));
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
            throw new UnsupportedUserException(
                sprintf('Expected an instance of %s, but got "%s".', User::class, \get_class($user))
            );
        }

        $property = $this->getProperty($response);
        $username = $response->getUsername();

        if (!empty($previousUser = $this->userManager->findUserBy([$property => $username]))) {
            $this->disconnect($previousUser, $response);
        }

        $user->updateOauth2($response->getResourceOwner()->getName(), $username, $response->getAccessToken());

        $this->userManager->updateUser($user);
    }

    /**
     * {@inheritdoc}
     */
    public function disconnect(UserInterface $user, UserResponseInterface $response): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('$user must be instance of %s', User::class));
        }

        $user->updateOauth2($response->getResourceOwner()->getName(), null, null);

        $this->userManager->updateUser($user);
    }
}
