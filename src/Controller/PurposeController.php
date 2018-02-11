<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Payment;
use App\Entity\Purpose;
use App\Entity\User;
use App\Form\Model\PurposeModel;
use App\Form\Type\PurposeType;
use App\Repository\AreaRepository;
use App\Repository\PaymentRepository;
use App\Repository\PurposeRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/payment/type")
 */
final class PurposeController extends Controller
{
    /**
     * @var PurposeRepository
     */
    private $purposeRepository;

    /**
     * @var AreaRepository
     */
    private $areaRepository;

    /**
     * @var PaymentRepository
     */
    private $paymentRepository;

    public function __construct(
        PurposeRepository $purposeRepository,
        AreaRepository $areaRepository,
        PaymentRepository $paymentRepository
    ) {
        $this->purposeRepository = $purposeRepository;
        $this->areaRepository = $areaRepository;
        $this->paymentRepository = $paymentRepository;
    }

    /**
     * @Route(name="purpose_index", defaults={"page": 1})
     * @Route("/page/{page}", requirements={"page": "[1-9]\d*"}, name="purposes_index_paginated")
     */
    public function indexAction($page)
    {
        return $this->render('purpose/index.html.twig', [
            'purposes' => $this->purposeRepository->findLatest($page),
        ]);
    }

    /**
     * @Route("/new", name="purpose_new")
     *
     * @Security("is_granted(constant('App\\Roles::CHAIRMAN'))")
     */
    public function newAction(Request $request)
    {
        $model = new PurposeModel();
        $form = $this->createForm(PurposeType::class, $model, [
            'action' => $this->generateUrl('purpose_new'),
            'areas' => $this->areaRepository->findPayable(),
        ]);

        if ($form->handleRequest($request)->isValid()) {
            $entity = new Purpose($model->name, $model->amount, $model->schedule, $model->calculation);

            $this->purposeRepository->save($entity);
            $this->createPayments($entity, $model, $this->getUser());

            $this->addFlash('success', sprintf('Платежная цель "%s" создана!', $model->name));

            return $this->redirectToRoute('purpose_index');
        }

        return $this->render('purpose/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function createPayments(Purpose $purpose, PurposeModel $model, User $user): void
    {
        $amount = null;
        if (Purpose::CALCULATION_SHARE === $model->calculation) {
            $amount = (int) ceil($model->amount / count($model->areas));
        }

        foreach ($model->areas as $area) {
            if (!$amount) {
                if (Purpose::CALCULATION_SIZE === $model->calculation) {
                    $amount = $area->getSize() / 100 * $model->amount;
                } elseif (Purpose::CALCULATION_AREA === $model->calculation) {
                    $amount = $model->amount;
                } else {
                    throw new \DomainException(sprintf('Unknown calculation: "%s"', $model->calculation));
                }
            }

            if (0 < $amount) {
                $amount *= -1;
            }

            $this->paymentRepository->save(new Payment($area, $purpose, $user, $amount));
        }
    }
}
