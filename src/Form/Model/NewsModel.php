<?php

declare(strict_types=1);

namespace App\Form\Model;

use App\Entity\News;
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

    /**
     * @param News $news
     *
     * @return static
     */
    public static function fromEntity(News $news)
    {
        $model = new static();
        $model->title = $news->getTitle();
        $model->content = $news->getContent();
        $model->published = $news->isPublished();
        $model->internal = $news->isInternal();

        return $model;
    }
}
