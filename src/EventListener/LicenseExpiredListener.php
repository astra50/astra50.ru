<?php

declare(strict_types=1);

namespace App\EventListener;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Templating\EngineInterface;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class LicenseExpiredListener implements EventSubscriberInterface
{
    /**
     * @var ParameterBagInterface
     */
    private $parameterBag;

    /**
     * @var EngineInterface
     */
    private $engine;

    public function __construct(ParameterBagInterface $parameterBag, EngineInterface $engine)
    {
        $this->parameterBag = $parameterBag;
        $this->engine = $engine;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }

    public function onKernelRequest(GetResponseEvent $event): void
    {
        if (false === $this->parameterBag->get('license_expired')) {
            return;
        }

        $event->setResponse(new Response($this->engine->render('license_expired.html.twig')));
    }
}
