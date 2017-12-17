<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Purpose;
use App\Repository\AreaRepository;
use App\Repository\PurposeRepository;
use App\EventDispatcher\Payment\PurposeEvent;
use App\Events;
use App\Form\Model\PurposeModel;
use App\Form\Type\PurposeType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/payment/type")
 *
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class PurposeController extends BaseController
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
     * @param PurposeRepository $purposeRepository
     * @param AreaRepository    $areaRepository
     */
    public function __construct(PurposeRepository $purposeRepository, AreaRepository $areaRepository)
    {
        $this->purposeRepository = $purposeRepository;
        $this->areaRepository = $areaRepository;
    }

    /**
     * @Route(name="purpose_index", defaults={"page" : 1})
     * @Route("/page/{page}", requirements={"page" : "[1-9]\d*"}, name="purposes_index_paginated")
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
            $this->dispatch(Events::PAYMENT_TYPE_NEW, new PurposeEvent($entity, $model, $this->getUser()));

            $this->success(sprintf('Платежная цель "%s" создана!', $model->name));

            return $this->redirectToRoute('purpose_index');
        }

        return $this->render('purpose/edit.html.twig', [
           'form' => $form->createView(),
        ]);
    }
}
