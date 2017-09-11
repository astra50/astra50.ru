<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\Event;

/**
 * @method User getUser()
 *
 * @author Konstantin Grachev <me@grachevko.ru>
 */
abstract class BaseController extends Controller
{
    /**
     * @param       $eventName
     * @param Event $event
     */
    public function dispatch($eventName, Event $event): void
    {
        $this->container->get('event_dispatcher')->dispatch($eventName, $event);
    }

    /**
     * @param $message
     */
    protected function success($message): void
    {
        $this->addFlash('success', $message);
    }

    /**
     * @throws \BadMethodCallException
     */
    protected function get($id): void
    {
        throw new \BadMethodCallException('Inject service instead');
    }
}
