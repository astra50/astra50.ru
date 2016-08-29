<?php

namespace AppBundle\Entity;

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
    /**
     * @var UuidInterface
     *
     * @ORM\Id
     * @ORM\Column(type="uuid_binary")
     */
    protected $id;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn()
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

    /**
     * @param UuidInterface $id
     * @param User          $author
     * @param string        $title
     * @param string        $slug
     * @param string        $content
     * @param bool          $published
     * @param bool          $internal
     * @param \DateTime     $createdAt
     */
    public function __construct(UuidInterface $id, User $author, string $title, string $content, bool $internal)
    {
        $this->id = $id;
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
    public function update(string $title, string $content, bool $internal)
    {
        $this->title = $title;
        $this->content = $content;
        $this->internal = $internal;
        $this->updatedAt = new \DateTime();
    }

    public function publish()
    {
        $this->published = true;
    }

    public function unPublish()
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
     * @return boolean
     */
    public function isPublished(): bool
    {
        return $this->published;
    }

    /**
     * @return boolean
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
