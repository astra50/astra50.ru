<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Suggestion;
use App\Form\Model\Suggestion as SuggestionModel;
use App\Form\Type\SuggestionType;
use Doctrine\ORM\EntityManagerInterface;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/suggestions")
 */
final class SuggestionsController extends Controller
{
    private const SUGGESTIONS_PER_PAGE = 10;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    public function __construct(EntityManagerInterface $em, \Swift_Mailer $mailer)
    {
        $this->em = $em;
        $this->mailer = $mailer;
    }

    /**
     * @Route("/list", name="suggestions_index", defaults={"page": 1})
     * @Route("/list/page/{page}", name="suggestions_index_paginated")
     *
     * @Security("is_granted(constant('App\\Roles::CHAIRMAN'))")
     */
    public function indexAction($page)
    {
        $qb = $this->em->createQueryBuilder()
            ->select('entity')
            ->from(Suggestion::class, 'entity')
            ->orderBy('entity.createdAt', 'DESC');

        $paginator = new Pagerfanta(new DoctrineORMAdapter($qb, false));
        $paginator->setMaxPerPage(self::SUGGESTIONS_PER_PAGE);
        $paginator->setCurrentPage($page);

        return $this->render('suggestion/index.html.twig', [
            'suggestions' => $paginator,
        ]);
    }

    /**
     * @Route(name="suggestions_new")
     */
    public function newAction(Request $request)
    {
        $model = new SuggestionModel();

        $form = $this->createForm(SuggestionType::class, $model)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist(new Suggestion($model));
            $this->em->flush();

            $message = (new \Swift_Message())
                ->setFrom('no-reply@astra50.ru')
                ->setReplyTo($model->email)
                ->setTo(['kirillsidorov@gmail.com', '9266681152@mail.ru', 'preemiere@ya.ru'])
                ->setSubject(sprintf('Новое обращение: %s от %s', $model->type->getName(), $model->name))
                ->setBody(<<<TEXT
Имя: $model->name
Почта: $model->email
Телефон: $model->phone
Сообщение: $model->text
TEXT
                );

            $this->mailer->send($message);

            $this->addFlash('success', 'Ваше предложение отправлено.');

            return $this->redirectToRoute('suggestions_new');
        }

        return $this->render('suggestion/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
