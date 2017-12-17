<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\SuggestionRepository;
use App\Entity\Suggestion;
use App\Form\Model\Suggestion as SuggestionModel;
use App\Form\Type\SuggestionType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/suggestions")
 *
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class SuggestionsController extends BaseController
{
    /**
     * @var SuggestionRepository
     */
    private $repository;

    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    public function __construct(SuggestionRepository $repository, \Swift_Mailer $mailer)
    {
        $this->repository = $repository;
        $this->mailer = $mailer;
    }

    /**
     * @Route("/list", name="suggestions_index", defaults={"page" : 1})
     * @Route("/list/page/{page}", name="suggestions_index_paginated")
     */
    public function indexAction($page)
    {
        /** @var Suggestion[] $suggestions */
        $suggestions = $this->repository->findLatest($page);

        return $this->render('suggestion/index.html.twig', [
            'suggestions' => $suggestions,
        ]);
    }

    /**
     * @Route(name="suggestions_new")
     */
    public function newAction(Request $request)
    {
        $model = new SuggestionModel();

        $form = $this->createForm(SuggestionType::class, $model);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->repository->save(new Suggestion($model));

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
