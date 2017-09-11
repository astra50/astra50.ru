<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity
 *
 * @author Konstantin Grachev <me@grachevko.ru>
 */
class News
{
    const NUM_ITEMS = 3;

    /**
     * @var UuidInterface
     *
     * @ORM\Id
     * @ORM\Column(type="uuid_binary")
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator("\App\Doctrine\UuidGenerator")
     */
    protected $id;

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
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    protected $internal;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    protected $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $updatedAt;

    public function __construct(User $author, string $title, string $content, bool $internal)
    {
        $this->author = $author;
        $this->title = $title;
        $this->content = $content;
        $this->internal = $internal;
        $this->createdAt = new \DateTime();
    }

    /**
     * @param string $title
     * @param string $content
     * @param bool   $published
     * @param bool   $internal
     */
    public function update(string $title, string $content, bool $internal): void
    {
        $this->title = $title;
        $this->content = $content;
        $this->internal = $internal;
        $this->updatedAt = new \DateTime();
    }

    public function publish(): void
    {
        $this->published = true;
    }

    public function unPublish(): void
    {
        $this->published = false;
    }

    /**
     * @return UuidInterface
     */
    public function getId(): UuidInterface
    {
        return $this->id;
    }

    /**
     * @return User
     */
    public function getAuthor(): User
    {
        return $this->author;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @return string
     */
    public function getShortContent()
    {
        if (strpos($this->content, '====')) {
            return explode('====', $this->content, 2)[0];
        }
    }

    /**
     * @return string
     */
    public function getMainContent()
    {
        if (strpos($this->content, '====')) {
            return explode('====', $this->content, 2)[1];
        }

        return $this->content;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @return bool
     */
    public function isPublished(): bool
    {
        return $this->published;
    }

    /**
     * @return bool
     */
    public function isInternal(): bool
    {
        return $this->internal;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }
}