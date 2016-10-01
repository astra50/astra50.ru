<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
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
     * @param $message
     */
    protected function success($message)
    {
        $this->addFlash('success', $message);
    }

    /**
     * @param       $eventName
     * @param Event $event
     */
    public function dispatch($eventName, Event $event)
    {
        $this->container->get('event_dispatcher')->dispatch($eventName, $event);
    }

    /**
     * @throws \BadMethodCallException
     */
    protected function get($id)
    {
        throw new \BadMethodCallException('Inject service instead');
    }
}
