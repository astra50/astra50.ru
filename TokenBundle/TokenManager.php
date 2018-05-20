<?php

declare(strict_types=1);

namespace Grachevko\TokenBundle;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr;
use Grachevko\TokenBundle\Entity\Token;
use Grachevko\TokenBundle\Exception\TokenAlreadyExpiredException;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class TokenManager implements TokenGeneratorInterface, PayloadsProviderInterface
{
    private const RANDOM_BYTES_LENGTH = 36;

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
    public function lifetime(array $payloads, DateTimeInterface $expiredAt): string
    {
        return $this->createToken($payloads, false, $expiredAt);
    }

    /**
     * {@inheritdoc}
     */
    public function disposable(array $payloads, DateTimeInterface $expiredAt = null): string
    {
        return $this->createToken($payloads, true, $expiredAt);
    }

    /**
     * {@inheritdoc}
     */
    public function payloads(string $token): ?array
    {
        $expr = new Expr();

        $token = $this->em->createQueryBuilder()
            ->select('entity')
            ->from(Token::class, 'entity')
            ->where('entity.token = :token')
            ->andWhere($expr->orX(
                $expr->gte('entity.expiredAt', ':now'),
                $expr->isNull('entity.expiredAt')
            ))
            ->setParameters([
                'token' => $token,
                'now' => new DateTime(),
            ])
            ->getQuery()
            ->getOneOrNullResult();

        if (!$token instanceof Token) {
            return null;
        }

        if ($token->isDisposable()) {
            try {
                $token->expire();
                $this->em->flush();
            } catch (TokenAlreadyExpiredException $exception) {
                return null;
            }
        }

        return $token->getPayloads();
    }

    private function createToken(array $payloads, bool $disposable, DateTimeInterface $expiredAt = null)
    {
        $token = base64_encode(bin2hex(random_bytes(self::RANDOM_BYTES_LENGTH)));

        if ($expiredAt instanceof DateTime) {
            $expiredAt = DateTimeImmutable::createFromMutable($expiredAt);
        }

        $this->em->persist(new Token($token, $payloads, $disposable, $expiredAt));
        $this->em->flush();

        return $token;
    }
}
