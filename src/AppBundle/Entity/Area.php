<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity()
 */
class Area
{
    /**
     * @var UuidInterface
     *
     * @ORM\Id()
     * @ORM\Column(type="uuid_binary")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", unique=true)
     */
    private $number;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $size;

    /**
     * @param UuidInterface $id
     * @param string        $number
     * @param int           $size
     */
    public function __construct(UuidInterface $id, string $number, int $size)
    {
        $this->id = $id;
        $this->number = $number;
        $this->size = $size;
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getNumber(): string
    {
        return $this->number;
    }

    public function getSize(): int
    {
        return $this->size;
    }
}
