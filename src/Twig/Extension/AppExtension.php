<?php

declare(strict_types=1);

namespace App\Twig\Extension;

use DateTimeImmutable;
use Twig\TwigFunction;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
class AppExtension extends \Twig_Extension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('build', [$this, 'build']),
            new TwigFunction('build_time', [$this, 'buildTime']),
        ];
    }

    public function doInstanceOf($object, $class): bool
    {
        return $object instanceof $class;
    }

    public function build(): string
    {
        return getenv('APP_VERSION');
    }

    public function buildTime(): DateTimeImmutable
    {
        if ($time = getenv('APP_BUILD_TIME')) {
            return DateTimeImmutable::createFromFormat(DATE_RFC2822, $time);
        }

        return new DateTimeImmutable();
    }
}
