<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use HWI\Bundle\OAuthBundle\Connect\AccountConnectorInterface;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthAwareUserProviderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationServiceException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class Oauth2UserProvider implements OAuthAwareUserProviderInterface, AccountConnectorInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByOAuthUserResponse(UserResponseInterface $response): UserInterface
    {
        $email = $response->getEmail();
        $identifier = $response->{'getUsername'}();

        if (null === $identifier) {
            throw new AuthenticationServiceException('OAuth2 server return null username');
        }

        $user = $this->em->createQueryBuilder()
            ->select('user')
            ->from(User::class, 'user')
            ->join('user.credentials', 'credential')
            ->where('credential.identifier = :identifier')
            ->andWhere('credential.expiredAt IS NULL')
            ->setParameter('identifier', $identifier)
            ->getQuery()->getOneOrNullResult();

        if (!$user) {
            $user = $this->em->createQueryBuilder()
                ->select('user')
                ->from(User::class, 'user')
                ->where('user.username = :username')
                ->setParameter('username', $email)
                ->getQuery()->getOneOrNullResult();
        }

        if (!$user) {
            $user = new User();
            $user->setUsername($email);
            $user->setRealname($response->getRealName());

            $this->em->persist($user);
        }

        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('$user must be instance of %s', User::class));
        }

        $oauthToken = $response->getOAuthToken();
        $resourceOwner = $response->getResourceOwner();

        $user->authenticate($resourceOwner->getName(), (string) $identifier, $oauthToken->getRawToken());

        $this->em->flush();

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

        $username = (string) $response->{'getUsername'}();
        if (empty($username)) {
            throw new AuthenticationServiceException('Username not sent by service, may be error.');
        }

        $previousUser = $this->em->createQueryBuilder()
            ->select('user')
            ->from(User::class, 'user')
            ->join('user.credentials', 'credential')
            ->where('credential.identifier = :username')
            ->andWhere('credential.expiredAt IS NULL')
            ->setParameter('username', $username)
            ->getQuery()->getOneOrNullResult();

        $resourceOwnerName = $response->getResourceOwner()->getName();

        if ($previousUser instanceof User) {
            $previousUser->revokeCredential($resourceOwnerName);
        }

        $user->authenticate($resourceOwnerName, $username, $response->getOAuthToken()->getRawToken());

        $this->em->flush();
    }
}
