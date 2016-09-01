<?php

namespace AppBundle\Controller;

use AppBundle\Repository\AreaRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * @Route("/area", service="app.controller.area")
 *
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class AreaController extends Controller
{
    /**
     * @var AreaRepository
     */
    private $areaRepository;

    /**
     * @param AreaRepository $areaRepository
     */
    public function __construct(AreaRepository $areaRepository)
    {
        $this->areaRepository = $areaRepository;
    }

    /**
     * @Route(name="area_list")
     */
    public function listAction()
    {
        return $this->render(':area:list.html.twig', [
            'areas' => $this->areaRepository->findAll(),
        ]);
    }
}
