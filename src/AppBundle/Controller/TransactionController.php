<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Payment;
use AppBundle\Entity\Repository\AreaRepository;
use AppBundle\Entity\Repository\PaymentRepository;
use AppBundle\Entity\Repository\PurposeRepository;
use AppBundle\Form\Model\PaymentModel;
use AppBundle\Form\Type\PaymentType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/transaction", service="app.controller.transaction")
 *
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class TransactionController extends BaseController
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
    public function indexAction($page)
    {
        return $this->render(':transaction:index.html.twig', [
            'payments' => $this->paymentRepository->findLatest($page),
        ]);
    }

    /**
     * @Route("/new", name="transaction_new")
     */
    public function newAction(Request $request)
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

            $this->success(sprintf('Платеж по цели "%s" для участка "%s" на сумму "%s" создан!', $purpose->getName(), $area->getNumber(), $amount / 100));

            return $this->redirectToRoute('transaction_index');
        }

        return $this->render(':transaction:edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
