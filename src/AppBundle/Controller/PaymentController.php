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
use Uuid\Uuid;

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
     * @var PurposeRepository
     */
    private $purposeRepository;

    /**
     * @var AreaRepository
     */
    private $areaRepository;

    /**
     * @param PaymentRepository $paymentRepository
     * @param PurposeRepository $purposeRepository
     * @param AreaRepository    $areaRepository
     */
    public function __construct(PaymentRepository $paymentRepository, PurposeRepository $purposeRepository, AreaRepository $areaRepository)
    {
        $this->paymentRepository = $paymentRepository;
        $this->purposeRepository = $purposeRepository;
        $this->areaRepository = $areaRepository;
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
     * @Route("/new", name="payment_new")
     */
    public function newAction(Request $request)
    {
        $model = new PaymentModel();
        $areas = $this->areaRepository->findAllForChoices('number');
        $purposes = $this->purposeRepository->findActiveForChoices();

        $form = $this->createForm(PaymentType::class, $model, [
            'action' => $this->generateUrl('payment_new'),
            'areas' => $areas,
            'purposes' => $purposes,
        ]);

        if ($form->handleRequest($request)->isValid()) {
            $purpose = $this->purposeRepository->getReference($model->purpose);
            $area = $this->areaRepository->getReference($model->area);
            $user = $this->getUser();
            $amount = $model->isPositive ? $model->amount : $model->amount * -1;

            $entity = new Payment(Uuid::create(), $area, $purpose, $user, $amount);

            $this->paymentRepository->save($entity);

            $areaNumber = array_search($model->area, $areas, true);
            $purposeName = array_search($model->purpose, $purposes, true);
            $this->success(sprintf('Платеж по цели "%s" для участка "%s" на сумму "%s" создан!', $purposeName, $areaNumber, $amount / 100));

            return $this->redirectToRoute('payment_list');
        }

        return $this->render(':payment:edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
