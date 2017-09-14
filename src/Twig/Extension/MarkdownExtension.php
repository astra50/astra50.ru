<?php

declare(strict_types=1);

namespace App\Twig\Extension;

use App\Markdown\MarkdownInterface;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
class MarkdownExtension extends \Twig_Extension
{
    /**
     * @var MarkdownInterface
     */
    private $markdown;

    public function __construct(MarkdownInterface $markdown)
    {
        $this->markdown = $markdown;
    }

    public function getFilters(): array
    {
        return [
            new \Twig_SimpleFilter('markdown', [$this, 'markdown']),
        ];
    }

    public function markdown(string $string): string
    {
        return $this->markdown->toHtml($string);
    }
}
