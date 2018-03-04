<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Area;
use App\Entity\Payment;
use App\Entity\Purpose;
use App\Entity\User;
use App\Form\Type\PurposeType;
use Doctrine\ORM\EntityManagerInterface;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/purpose")
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
            $this->createPayments($purpose, $this->getUser());

            $this->em->flush();

            $this->addFlash('success', sprintf('Платежная цель "%s" создана!', $purpose->getName()));

            return $this->redirectToRoute('purpose_index');
        }

        return $this->render('purpose/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    private function createPayments(Purpose $purpose, User $user): void
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
                    return $shared ?: $shared = (int) ceil($purpose->getAmount() / count($purpose->getAreas()));
                };
                break;
            default:
                throw new \DomainException(sprintf('Unknown calculation: "%s"', $calculation->getName()));
        }

        foreach ($purpose->getAreas() as $area) {
            $amount = $calc($area, $purpose);

            if (0 < $amount) {
                $amount *= -1;
            }

            if (0 === $amount) {
                continue;
            }

            $this->em->persist(new Payment($area, $purpose, $user, $amount));
        }
    }
}
