<?php

declare(strict_types=1);

namespace App\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class StreetModel
{
    /**
     * @var string
     *
     * @Assert\NotBlank
     */
    public $name;
}
