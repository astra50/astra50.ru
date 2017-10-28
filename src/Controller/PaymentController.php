<?php

declare(strict_types=1);

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class PaymentController extends Controller
{
    /**
     * @Route("/payment/{type}", name="payment_index")
     */
    public function indexAction(string $type = null): Response
    {
        return $this->render('payment/index.html.twig');
    }
}
