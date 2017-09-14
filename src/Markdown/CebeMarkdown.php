<?php

declare(strict_types=1);

namespace App\Markdown;

use cebe\markdown\GithubMarkdown;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class CebeMarkdown implements MarkdownInterface
{
    /**
     * @var GithubMarkdown
     */
    private $markdown;

    public function __construct(GithubMarkdown $markdown)
    {
        $this->markdown = $markdown;
    }

    public function toHtml(string $string): string
    {
        return $this->markdown->parse($string);
    }
}
