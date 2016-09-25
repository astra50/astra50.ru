<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Symfony\Component\EventDispatcher\Event;

/**
 * @method User getUser()
 *
 * @author Konstantin Grachev <me@grachevko.ru>
 */
abstract class Controller extends \Symfony\Bundle\FrameworkBundle\Controller\Controller
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
        $this->get('event_dispatcher')->dispatch($eventName, $event);
    }
}
