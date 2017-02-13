<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class UnderConstructionController extends BaseController
{
    /**
     * @Route("/suggestions", name="suggestions_index")
     * @Route("/payment", name="payment_index")
     * @Route("/report", name="report_index")
     */
    public function indexAction()
    {
        return $this->render('under_construction.html.twig');
    }
}
