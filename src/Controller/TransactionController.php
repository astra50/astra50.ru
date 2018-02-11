<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Payment;
use App\Form\Model\PaymentModel;
use App\Form\Type\PaymentType;
use App\Repository\AreaRepository;
use App\Repository\PaymentRepository;
use App\Repository\PurposeRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/transaction")
 *
 * @Security("is_granted(constant('App\\Roles::CASHIER'))")
 */
final class TransactionController extends Controller
{
    /**
     * @var PaymentRepository
     */
    private $paymentRepository;

    /**
     * @var PurposeRepository
     */
    private $purposeRepository;

    /**
     * @var AreaRepository
     */
    private $areaRepository;

    public function __construct(
        PaymentRepository $paymentRepository,
        PurposeRepository $purposeRepository,
        AreaRepository $areaRepository
    ) {
        $this->paymentRepository = $paymentRepository;
        $this->purposeRepository = $purposeRepository;
        $this->areaRepository = $areaRepository;
    }

    /**
     * @Route("/", name="transaction_index", defaults={"page": 1})
     * @Route("/page/{page}", requirements={"page": "[1-9]\d*"}, name="transaction_index_paginated")
     */
    public function indexAction(int $page): Response
    {
        return $this->render('transaction/index.html.twig', [
            'payments' => $this->paymentRepository->findLatest($page),
        ]);
    }

    /**
     * @Route("/new", name="transaction_new")
     */
    public function newAction(Request $request): Response
    {
        $model = new PaymentModel();
        $areas = $this->areaRepository->findPayable();
        $purposes = $this->purposeRepository->findActive();

        $form = $this->createForm(PaymentType::class, $model, [
            'action' => $this->generateUrl('transaction_new'),
            'areas' => $areas,
            'purposes' => $purposes,
        ]);

        if ($form->handleRequest($request)->isValid()) {
            $area = $model->area;
            $purpose = $model->purpose;
            $user = $this->getUser();
            $amount = $model->isPositive ? $model->amount : $model->amount * -1;

            $entity = new Payment($area, $purpose, $user, $amount);

            $this->paymentRepository->save($entity);

            $this->addFlash('success', sprintf('Платеж по цели "%s" для участка "%s" на сумму "%s" создан!', $purpose->getName(), $area->getNumber(), $amount / 100));

            return $this->redirectToRoute('transaction_index');
        }

        return $this->render('transaction/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
