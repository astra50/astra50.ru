<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Area;
use App\Entity\Street;
use App\Entity\User;
use App\Form\Model\AreaModel;
use App\Form\Type\AreaType;
use App\Repository\AreaRepository;
use App\Repository\PaymentRepository;
use App\Repository\StreetRepository;
use App\Repository\UserRepository;
use App\Roles;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/area")
 *
 * @Security("is_granted(constant('App\\Roles::CHAIRMAN'))")
 */
final class AreaController extends Controller
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
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(
        AreaRepository $areaRepository,
        PaymentRepository $paymentRepository,
        StreetRepository $streetRepository,
        UserRepository $userRepository,
        EntityManagerInterface $em
    ) {
        $this->areaRepository = $areaRepository;
        $this->paymentRepository = $paymentRepository;
        $this->streetRepository = $streetRepository;
        $this->userRepository = $userRepository;
        $this->em = $em;
    }

    /**
     * @Route(name="area_index")
     */
    public function indexAction()
    {
        return $this->render('/area/index.html.twig', [
            'areas' => $this->areaRepository->findAllWithOwners(),
        ]);
    }

    /**
     * @Route("/{number}", name="area_show", defaults={"page": 1}, requirements={"page": "\d"})
     */
    public function showAction(Area $area, $page)
    {
        $payments = $this->paymentRepository->paginatePurposesByArea($area, $page);
        $balance = $this->paymentRepository->getBalanceFromActivePurposesByArea($area);

        return $this->render('area/show.html.twig', [
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
            'streets' => $this->em->getRepository(Street::class)->findAll(),
            'users' => $this->em->getRepository(User::class)->findAll(),
        ]);

        if ($form->handleRequest($request)->isValid()) {
            $area->setSize($model->size);
            if ($model->street) {
                $area->setStreet($model->street);
            }

            $area->replaceUsers($model->users);

            $this->areaRepository->save($area);

            foreach ($model->users as $user) {
                $user->addRole(Roles::COMMUNITY);

                $this->userRepository->save($user);
            }

            return $this->redirectToRoute('area_index');
        }

        return $this->render('area/edit.html.twig', [
            'area' => $area,
            'form' => $form->createView(),
        ]);
    }
}
