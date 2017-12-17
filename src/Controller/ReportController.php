<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Enum\ReportType;
use App\Entity\Report;
use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/reports", name="report_")
 */
final class ReportController extends Controller
{
    /**
     * @var EntityManager
     */
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @Route(name="index")
     */
    public function indexAction()
    {
        $repository = $this->em->getRepository(Report::class);

        $orderBy = ['year' => 'DESC', 'month' => 'DESC'];

        $reports = [
            $repository->findBy(['type' => ReportType::financial()], $orderBy),
            $repository->findBy(['type' => ReportType::accounting()], $orderBy),
            $repository->findBy(['type' => ReportType::project()], $orderBy),
        ];

        return $this->render('report/index.html.twig', [
            'chunkedReports' => $reports,
        ]);
    }

    /**
     * @Route("/new", name="new")
     */
    public function newAction(Request $request)
    {
        $report = new Report();

        $form = $this->createForm(\App\Form\Type\ReportType::class, $report);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($report);
            $this->em->flush();

            $this->addFlash('success', sprintf('Отчёт "%s" создан!', $report->getName()));

            if ($request->request->getBoolean('save_and_new')) {
                return $this->redirectToRoute('report_new');
            }

            return $this->redirectToRoute('report_index');
        }

        return $this->render('report/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="edit")
     */
    public function editAction(Report $report, Request $request)
    {
        $form = $this->createForm(\App\Form\Type\ReportType::class, $report);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();

            return $this->redirectToRoute('report_index');
        }

        return $this->render('report/edit.html.twig', [
            'form' => $form->createView(),
            'entity' => $report,
        ]);
    }
}
