<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Area;
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
        switch (true) {
            case Purpose::CALCULATION_SIZE === $model->calculation:
                $calc = function (Area $area, PurposeModel $model) {
                    return $area->getSize() / 100 * $model->amount;
                };
                break;
            case Purpose::CALCULATION_AREA === $model->calculation:
                $calc = function (Area $area, PurposeModel $model) {
                    return $model->amount;
                };
                break;
            case Purpose::CALCULATION_SHARE === $model->calculation:
                $shared = null;
                $calc = function (Area $area, PurposeModel $model) use (&$shared) {
                    return $shared ?: $shared = (int) ceil($model->amount / count($model->areas));
                };
                break;
            default:
                throw new \DomainException(sprintf('Unknown calculation: "%s"', $model->calculation));
        }

        foreach ($model->areas as $area) {
            $amount = $calc($area, $model);

            if (0 < $amount) {
                $amount *= -1;
            }

            $this->paymentRepository->save(new Payment($area, $purpose, $user, $amount));
        }
    }
}
