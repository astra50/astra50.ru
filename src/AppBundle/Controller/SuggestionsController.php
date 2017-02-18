<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Repository\SuggestionRepository;
use AppBundle\Entity\Suggestion;
use AppBundle\Form\Model\Suggestion as SuggestionModel;
use AppBundle\Form\Type\SuggestionType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/suggestions", service="app.controller.suggestion")
 *
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class SuggestionsController extends BaseController
{
    /**
     * @var SuggestionRepository
     */
    private $repository;

    public function __construct(SuggestionRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @Route("/list", name="suggestions_index", defaults={"page": 1})
     * @Route("/list/page/{page}", name="suggestions_index_paginated")
     */
    public function indexAction($page)
    {
        /** @var Suggestion[] $suggestions */
        $suggestions = $this->repository->findLatest($page);

        return $this->render(':suggestion:index.html.twig', [
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

            $this->addFlash('success', 'Ваша предложение отправлено.');

            return $this->redirectToRoute('suggestions_new');
        }

        return $this->render(':suggestion:new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
