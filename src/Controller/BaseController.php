<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @method User getUser()
 *
 * @author Konstantin Grachev <me@grachevko.ru>
 */
abstract class BaseController extends Controller
{
    public function dispatch(string $eventName, Event $event): void
    {
        $this->container->get(EventDispatcherInterface::class)->dispatch($eventName, $event);
    }

    protected function success(string $message): void
    {
        $this->addFlash('success', $message);
    }

    protected function get($id): void
    {
        throw new \BadMethodCallException('Inject service instead');
    }
}
