<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Street;
use AppBundle\Form\Model\StreetModel;
use AppBundle\Form\Type\StreetType;
use AppBundle\Entity\Repository\StreetRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Uuid\Uuid;

/**
 * @Route("/street", service="app.controller.street")
 */
class StreetController extends BaseController
{
    /**
     * @var StreetRepository
     */
    private $streetRepository;

    /**
     * @param StreetRepository $streetRepository
     */
    public function __construct(StreetRepository $streetRepository)
    {
        $this->streetRepository = $streetRepository;
    }

    /**
     * @Route("/", name="street_list")
     */
    public function listAction()
    {
        $streets = $this->streetRepository->findAll();

        return $this->render(':street:list.html.twig', array(
            'streets' => $streets,
        ));
    }

    /**
     * @Route("/new", name="street_new")
     */
    public function newAction(Request $request)
    {
        $streetModel = new StreetModel();
        $form = $this->createForm(StreetType::class, $streetModel);

        if ($form->handleRequest($request)->isValid()) {
            $street = new Street(Uuid::create(), $streetModel->name);
            $this->streetRepository->save($street);

            return $this->redirectToRoute('street_list');
        }

        return $this->render(':street:edit.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}
