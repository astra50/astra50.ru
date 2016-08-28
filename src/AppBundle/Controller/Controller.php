<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;

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
}
