<?php

declare(strict_types=1);

namespace App\Twig\Extension;

use DateTimeImmutable;
use LogicException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
class AppExtension extends AbstractExtension
{
    /**
     * @var ParameterBagInterface
     */
    private $parameterBag;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->parameterBag = $parameterBag;
    }

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
        return $this->parameterBag->get('app_version');
    }

    public function buildTime(): DateTimeImmutable
    {
        $string = $this->parameterBag->get('app_build_time');
        $object = DateTimeImmutable::createFromFormat(DATE_RFC2822, $string);

        if (!$object instanceof DateTimeImmutable) {
            throw new LogicException(
                sprintf('Can\'t create "%s" from string "%s"', DateTimeImmutable::class, $string)
            );
        }

        return $object;
    }
}
