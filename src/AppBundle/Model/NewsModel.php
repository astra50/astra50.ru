<?php

namespace AppBundle\Model;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class NewsModel
{
    /**
     * @var string
     *
     * @Assert\NotBlank(message="Заголовок не может быть пустым.")
     */
    public $title;

    /**
     * @var string
     *
     * @Assert\NotBlank(message="Содержание не может быть пустым.")
     */
    public $content;

    /**
     * @var bool
     *
     * @Assert\Type("boolean")
     */
    public $published;

    /**
     * @var bool
     *
     * @Assert\Type("boolean")
     */
    public $internal;
}
