<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * @Route("/payment", service="app.controller.payment")
 *
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class PaymentController extends BaseController
{
    /**
     * @Route("")
     */
    public function newAction()
    {

    }
}
