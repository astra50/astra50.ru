<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Area;
use AppBundle\Entity\Repository\AreaRepository;
use AppBundle\Entity\Repository\PaymentRepository;
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
     * @param AreaRepository $areaRepository
     */
    public function __construct(AreaRepository $areaRepository, PaymentRepository $paymentRepository)
    {
        $this->areaRepository = $areaRepository;
        $this->paymentRepository = $paymentRepository;
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

        return $this->render(':area:show.html.twig', [
            'area' => $area,
            'pagerfanta' => $payments,
        ]);
    }
}
