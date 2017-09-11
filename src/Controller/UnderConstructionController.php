<?php

declare(strict_types=1);

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class UnderConstructionController extends BaseController
{
    /**
     * @Route("/payment", name="payment_index")
     * @Route("/report", name="report_index")
     */
    public function indexAction()
    {
        return $this->render('under_construction.html.twig');
    }
}
