<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Repository\StreetRepository;
use AppBundle\Entity\Street;
use AppBundle\Form\Model\StreetModel;
use AppBundle\Form\Type\StreetType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

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
     * @Route("/", name="street_index")
     */
    public function indexAction()
    {
        $streets = $this->streetRepository->findAll();

        return $this->render(':street:index.html.twig', [
            'streets' => $streets,
        ]);
    }

    /**
     * @Route("/new", name="street_new")
     */
    public function newAction(Request $request)
    {
        $streetModel = new StreetModel();
        $form = $this->createForm(StreetType::class, $streetModel);

        if ($form->handleRequest($request)->isValid()) {
            $street = new Street($streetModel->name);
            $this->streetRepository->save($street);

            return $this->redirectToRoute('street_index');
        }

        return $this->render(':street:edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
