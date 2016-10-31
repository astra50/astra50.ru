<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Area;
use AppBundle\Entity\Repository\AreaRepository;
use AppBundle\Entity\Repository\PaymentRepository;
use AppBundle\Entity\Repository\StreetRepository;
use AppBundle\Entity\Repository\UserRepository;
use AppBundle\Form\Model\AreaModel;
use AppBundle\Form\Type\AreaType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/area", service="app.controller.area")
 *
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class AreaController extends BaseController
{
    /**
     * @var AreaRepository
     */
    private $areaRepository;

    /**
     * @var PaymentRepository
     */
    private $paymentRepository;

    /**
     * @var StreetRepository
     */
    private $streetRepository;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @param AreaRepository $areaRepository
     */
    public function __construct(AreaRepository $areaRepository, PaymentRepository $paymentRepository, StreetRepository $streetRepository, UserRepository $userRepository)
    {
        $this->areaRepository = $areaRepository;
        $this->paymentRepository = $paymentRepository;
        $this->streetRepository = $streetRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * @Route(name="area_list")
     */
    public function listAction()
    {
        return $this->render(':area:list.html.twig', [
            'areas' => $this->areaRepository->findAllWithOwners(),
        ]);
    }

    /**
     * @Route("/{number}", name="area_show")
     */
    public function showAction(Request $request, Area $area)
    {
        $pageSize = 10;
        $pageIndex = $request->query->get('page', 1);

        $payments = $this->paymentRepository->paginatePurposesByArea($area, $pageSize, $pageIndex);
        $balance = $this->paymentRepository->getBalanceFromActivePurposesByArea($area);

        return $this->render(':area:show.html.twig', [
            'area' => $area,
            'pagerfanta' => $payments,
            'balance' => $balance,
        ]);
    }

    /**
     * @Route("/{number}/edit", name="area_edit")
     */
    public function editAction(Request $request, Area $area)
    {
        $model = AreaModel::fromEntity($area);

        $form = $this->createForm(AreaType::class, $model, [
            'streets' => $this->streetRepository->findAllForChoices('name'),
            'users' => $this->userRepository->findAllForChoices('realname'),
        ]);

        if ($form->handleRequest($request)->isValid()) {
            $area->setSize($model->size);
            $area->setStreet($model->street ? $this->streetRepository->getReference($model->street) : null);
            $area->replaceUsers($this->userRepository->getReferences($model->users));

            $this->areaRepository->save($area);

            foreach ($model->users as $id) {
                $user = $this->userRepository->get($id);
                $user->addRole(\AppRoles::COMMUNITY);

                $this->userRepository->save($user);
            }

            return $this->redirectToRoute('area_list');
        }

        return $this->render(':area:edit.html.twig', [
            'area' => $area,
            'form' => $form->createView(),
        ]);
    }
}
