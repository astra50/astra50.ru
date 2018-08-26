<?php

declare(strict_types=1);

namespace App\Entity;

use App\Doctrine\ORM\Mapping\Traits\Identity;
use App\Entity\Enum\SuggestionType;
use App\Form\Model\Suggestion as Model;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Suggestion
{
    use Identity;

    /**
     * @var string
     *
     * @ORM\Column
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column
     */
    private $phone;

    /**
     * @var string
     *
     * @ORM\Column
     */
    private $email;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     */
    private $text;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    public function __construct(Model $model = null)
    {
        $this->name = $model->name;
        $this->phone = $model->phone;
        $this->email = $model->email;
        $this->type = $model->type->getId();
        $this->text = $model->text;
        $this->createdAt = new \DateTime();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getType(): SuggestionType
    {
        return new SuggestionType($this->type);
    }

    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return clone $this->createdAt;
    }
}
