<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Street;
use App\Form\Model\StreetModel;
use App\Form\Type\StreetType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/street")
 *
 * @Security("is_granted(constant('App\\Roles::CHAIRMAN'))")
 */
class StreetController extends Controller
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/", name="street_index")
     */
    public function indexAction()
    {
        $streets = $this->em->getRepository(Street::class)->findAll();

        return $this->render('street/index.html.twig', [
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

            $this->em->persist($street);
            $this->em->flush();

            return $this->redirectToRoute('street_index');
        }

        return $this->render('street/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
