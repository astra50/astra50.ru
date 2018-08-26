<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Area;
use App\Entity\Payment;
use App\Entity\Purpose;
use App\Entity\User;
use App\Form\Type\PurposeType;
use App\Roles;
use Doctrine\ORM\EntityManagerInterface;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/purpose")
 *
 * @Security("is_granted(constant('App\\Roles::CHAIRMAN'))")
 */
final class PurposeController extends Controller
{
    private const PURPOSES_PER_PAGE = 20;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route(name="purpose_index", defaults={"page": 1})
     * @Route("/page/{page}", requirements={"page": "[1-9]\d*"}, name="purposes_index_paginated")
     */
    public function indexAction($page)
    {
        $qb = $this->em->createQueryBuilder()
            ->select('entity')
            ->from(Purpose::class, 'entity')
            ->orderBy('entity.createdAt', 'DESC');

        $paginator = new Pagerfanta(new DoctrineORMAdapter($qb, false));
        $paginator->setMaxPerPage(self::PURPOSES_PER_PAGE);
        $paginator->setCurrentPage($page);

        return $this->render('purpose/index.html.twig', [
            'purposes' => $paginator,
        ]);
    }

    /**
     * @Route("/new", name="purpose_new")
     *
     * @Security("is_granted(constant('App\\Roles::CHAIRMAN'))")
     */
    public function newAction(Request $request)
    {
        $purpose = new Purpose();
        $form = $this->createForm(PurposeType::class, $purpose, [
            'action' => $this->generateUrl('purpose_new'),
        ])
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($purpose);
            $this->em->flush();

            $this->addFlash('success', sprintf('Платежная цель "%s" создана!', $purpose->getName()));

            return $this->redirectToRoute('purpose_index');
        }

        return $this->render('purpose/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="purpose_edit")
     */
    public function editAction(Request $request, Purpose $purpose)
    {
        if (!$purpose->isEditable()) {
            $this->addFlash('error', sprintf('Цель %s более редактировать нельзя!', $purpose->getName()));

            return $this->redirectToRoute('purpose_index');
        }

        $form = $this->createForm(PurposeType::class, $purpose, [
            'name_disabled' => !$this->isGranted(Roles::ADMIN),
        ])
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();

            $this->addFlash('success', sprintf('Цель "%s" изменена!', $purpose->getName()));

            return $this->redirectToRoute('purpose_index');
        }

        return $this->render('purpose/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/payment", name="purpose_payment")
     */
    public function paymentAction(Purpose $purpose, Request $request)
    {
        $comment = $request->isMethod('POST')
            ? $request->request->get('form')['comment']
            : sprintf('# %s', $purpose->getName());

        $payments = $this->createPayments($purpose, $this->getUser(), $comment);

        $form = $this->createFormBuilder()
            ->add('comment', TextType::class, [
                'label' => 'Комментарий',
                'mapped' => false,
                'data' => $comment,
            ])
            ->getForm()
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();

            $this->addFlash(
                'success',
                sprintf('"%s" платежей по цени "%s" созданы!', \count($payments), $purpose->getName())
            );

            return $this->redirectToRoute('transaction_index');
        }

        return $this->render('purpose/payment.html.twig', [
            'purpose' => $purpose,
            'payments' => $payments,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/archive", name="purpose_archive")
     */
    public function archiveAction(Purpose $purpose, Request $request)
    {
        if (null !== $purpose->getArchivedAt()) {
            $this->addFlash('error', sprintf('Цель "%s" уже архивная!', $purpose->getName()));
        }

        $form = $this->createFormBuilder()->getForm()->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $purpose->archive();
            $this->em->flush();

            $this->addFlash('success', sprintf('Цель "%s" заархивирована!', $purpose->getName()));

            return $this->redirectToRoute('purpose_index');
        }

        return $this->render('purpose/archive.html.twig', [
            'purpose' => $purpose,
            'form' => $form->createView(),
        ]);
    }

    private function createPayments(Purpose $purpose, User $user, string $comment): array
    {
        $calculation = $purpose->getCalculation();

        switch (true) {
            case $calculation->isSize():
                $calc = function (Area $area, Purpose $purpose) {
                    return $area->getSize() / 100 * $purpose->getAmount();
                };
                break;
            case $calculation->isArea():
                $calc = function (Area $area, Purpose $purpose) {
                    return $purpose->getAmount();
                };
                break;
            case $calculation->isShare():
                $shared = null;
                $calc = function (Area $area, Purpose $purpose) use (&$shared) {
                    if (null === $shared) {
                        return $shared = ceil($purpose->getAmount() / \count($purpose->getAreas()));
                    }

                    return $shared;
                };
                break;
            default:
                throw new \DomainException(sprintf('Unknown calculation: "%s"', $calculation->getName()));
        }

        $payments = [];
        foreach ($purpose->getAreas() as $area) {
            $amount = $calc($area, $purpose);

            if (0 < $amount) {
                $amount = -$amount;
            }

            if (0 === $amount) {
                continue;
            }

            $this->em->persist($payments[] = new Payment($area, $purpose, $user, (int) $amount, $comment));
        }

        return $payments;
    }
}
