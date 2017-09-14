<?php

declare(strict_types=1);

namespace App\Markdown;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
interface MarkdownInterface
{
    public function toHtml(string $string): string;
}
