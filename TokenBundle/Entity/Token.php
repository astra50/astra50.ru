<?php

declare(strict_types=1);

namespace Grachevko\TokenBundle\Entity;

use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Grachevko\TokenBundle\Exception\TokenAlreadyExpiredException;

/**
 * @ORM\Entity
 *
 * @ORM\Table(indexes={
 *     @ORM\Index(name="token_search_idx", columns={"token", "expired_at"})
 * })
 */
class Token
{
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
     * @ORM\Column
     */
    private $token;

    /**
     * @var array
     *
     * @ORM\Column(type="array")
     */
    private $payloads;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    private $disposable;

    /**
     * @var DateTimeImmutable
     *
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $expiredAt;

    /**
     * @var DateTimeImmutable
     *
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    public function __construct(string $token, array $payloads, bool $disposable, DateTimeInterface $expiredAt = null)
    {
        $this->token = $token;
        $this->payloads = $payloads;
        $this->disposable = $disposable;
        $this->createdAt = new DateTimeImmutable();

        if (null !== $expiredAt) {
            $this->expiredAt = clone $expiredAt;
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function isDisposable(): bool
    {
        return $this->disposable;
    }

    public function getExpiredAt(): DateTimeImmutable
    {
        return $this->expiredAt;
    }

    public function getPayloads(): array
    {
        return $this->payloads;
    }

    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * @throws TokenAlreadyExpiredException
     */
    public function expire(): void
    {
        $now = new DateTimeImmutable();

        if (null === $this->expiredAt) {
            $this->expiredAt = $now;

            return;
        }

        if (0 <= ($now->getTimestamp() - $this->expiredAt->getTimestamp())) {
            throw new TokenAlreadyExpiredException('Can\'t expire, already expired!');
        }

        $this->expiredAt = $now;
    }
}
