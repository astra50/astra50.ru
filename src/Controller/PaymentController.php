<?php

declare(strict_types=1);

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/payment")
 */
final class PaymentController extends Controller
{
    /**
     * @Route(name="payment_index")
     */
    public function indexAction()
    {
        $payments = [
            'sbrf' => 'Оплата в отделении СберБанка',
            'sbrf-mobile' => 'Мобильном приложении СберБанка',
            'sbrf-online' => 'СберБанк Онлайн',
            'transfer' => 'Оплата по реквизитам СНТ «Астра»',
            'tinkoff' => 'Перевод на карту Tinkoff',
            'cash' => 'Наличные средства',
        ];

        return $this->render('payment/index.html.twig', [
            'payments' => $payments,
        ]);
    }

    /**
     * @Route("/{type}", name="payment_type",
     *     requirements={"type" : "sbrf|sbrf-mobile|sbrf-online|transfer|tinkoff|cash"}
     * )
     */
    public function typeAction(string $type): Response
    {
        return $this->render(sprintf('payment/type-%s.html.twig', $type));
    }
}
