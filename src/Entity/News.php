<?php

declare(strict_types=1);

namespace App\Entity;

use App\Doctrine\ORM\Mapping\Traits\Identity;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity
 *
 * @author Konstantin Grachev <me@grachevko.ru>
 */
class News
{
    use Identity;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    protected $author;

    /**
     * @var string
     *
     * @ORM\Column
     */
    protected $title;

    /**
     * @var string
     *
     * @ORM\Column(unique=true)
     *
     * @Gedmo\Slug(fields={"title"}, unique=true)
     */
    protected $slug;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     */
    protected $content;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    protected $published = false;

    /**
     * @var DateTimeImmutable
     *
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    protected $publishedAt;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    protected $internal;

    /**
     * @var DateTimeImmutable
     *
     * @ORM\Column(type="datetime_immutable")
     */
    protected $createdAt;

    /**
     * @var DateTimeImmutable
     *
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    protected $updatedAt;

    public function __construct(User $author, string $title, string $content, bool $internal)
    {
        $this->author = $author;
        $this->title = $title;
        $this->content = $content;
        $this->internal = $internal;
        $this->createdAt = new DateTimeImmutable();
    }

    public function update(string $title, string $content, bool $internal): void
    {
        $this->title = $title;
        $this->content = $content;
        $this->internal = $internal;
        $this->updatedAt = new DateTimeImmutable();
    }

    public function publish(): void
    {
        $this->published = true;

        if (!$this->publishedAt) {
            $this->publishedAt = new DateTimeImmutable();
        }
    }

    public function unPublish(): void
    {
        $this->published = false;
    }

    public function getAuthor(): User
    {
        return $this->author;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getShortContent(): ?string
    {
        if (strpos($this->content, '====')) {
            return explode('====', $this->content, 2)[0];
        }

        return null;
    }

    public function getMainContent(): ?string
    {
        if (strpos($this->content, '====')) {
            return explode('====', $this->content, 2)[1];
        }

        return $this->content;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function isPublished(): bool
    {
        return $this->published;
    }

    public function getPublishedAt(): DateTimeImmutable
    {
        return $this->publishedAt;
    }

    public function isInternal(): bool
    {
        return $this->internal;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }
}
