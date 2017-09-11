<?php

declare(strict_types=1);

namespace App\Form\Model;

use App\Entity\Enum\SuggestionType;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class Suggestion
{
    /**
     * @var string
     *
     * @Assert\NotBlank
     */
    public $name;

    /**
     * @var string
     *
     * @Assert\NotBlank
     */
    public $phone;

    /**
     * @var string
     *
     * @Assert\NotBlank
     * @Assert\Email
     */
    public $email;

    /**
     * @var SuggestionType
     */
    public $type;

    /**
     * @var string
     *
     * @Assert\NotBlank
     */
    public $text;
}
