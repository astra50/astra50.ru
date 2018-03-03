<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Payment;
use App\Form\Model\PaymentModel;
use App\Form\Type\PaymentType;
use Doctrine\ORM\EntityManagerInterface;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/transaction")
 *
 * @Security("is_granted(constant('App\\Roles::CASHIER'))")
 */
final class TransactionController extends Controller
{
    private const PAYMENTS_PER_PAGe = 50;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/", name="transaction_index", defaults={"page": 1})
     * @Route("/page/{page}", requirements={"page": "[1-9]\d*"}, name="transaction_index_paginated")
     */
    public function indexAction(int $page): Response
    {
        $qb = $this->em->createQueryBuilder()
            ->select('entity')
            ->from(Payment::class, 'entity')
            ->orderBy('entity.createdAt', 'DESC');

        $paginator = new Pagerfanta(new DoctrineORMAdapter($qb, false));
        $paginator->setMaxPerPage(self::PAYMENTS_PER_PAGe);
        $paginator->setCurrentPage($page);

        return $this->render('transaction/index.html.twig', [
            'payments' => $paginator,
        ]);
    }

    /**
     * @Route("/new", name="transaction_new")
     */
    public function newAction(Request $request): Response
    {
        $model = new PaymentModel();

        $form = $this->createForm(PaymentType::class, $model, [
            'action' => $this->generateUrl('transaction_new'),
        ])
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $area = $model->area;
            $purpose = $model->purpose;
            $user = $this->getUser();
            $amount = $model->isPositive ? $model->amount : $model->amount * -1;

            $this->em->persist(new Payment($area, $purpose, $user, $amount, $model->comment));
            $this->em->flush();

            $this->addFlash('success', sprintf('Платеж по цели "%s" для участка "%s" на сумму "%s" создан!', $purpose->getName(), $area->getNumber(), $amount / 100));

            return $this->redirectToRoute('transaction_index');
        }

        return $this->render('transaction/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
