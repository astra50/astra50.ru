<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Repository\PaymentRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/payment", service="app.controller.payment")
 *
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class PaymentController extends BaseController
{
    /**
     * @var PaymentRepository
     */
    private $paymentRepository;

    /**
     */
    public function __construct(PaymentRepository $paymentRepository)
    {
        $this->paymentRepository = $paymentRepository;
    }

    /**
     * @Route("/", name="payment_list")
     */
    public function listAction(Request $request)
    {
        $pageSize = 50;
        $pageIndex = $request->query->get('page', 1);

        return $this->render(':payment:list.html.twig', [
            'pagerfanta' => $this->paymentRepository->paginateLatest($pageSize, $pageIndex),
        ]);
    }

    /**
     * @Route("")
     */
    public function newAction()
    {

    }
}
