<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\News;
use App\Form\Model\NewsModel;
use App\Form\Type\NewsType;
use App\Repository\NewsRepository;
use App\Roles;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class NewsController extends Controller
{
    /**
     * @var NewsRepository
     */
    private $newsRepository;

    public function __construct(NewsRepository $newsRepository)
    {
        $this->newsRepository = $newsRepository;
    }

    /**
     * @Route("/", name="news_index", defaults={"page": 1})
     * @Route("/page/{page}", requirements={"page": "[1-9]\d*"}, name="news_index_paginated")
     */
    public function indexAction(int $page)
    {
        $news = $this->newsRepository->findLatest(
            $page,
            !$this->isGranted(Roles::NEWS_WRITER),
            !$this->isGranted(Roles::COMMUNITY)
        );

        return $this->render('news/index.html.twig', [
            'news' => $news,
        ]);
    }

    /**
     * @Route("/news/new", name="news_new")
     *
     * @Security("is_granted(constant('App\\Roles::NEWS_WRITER'))")
     */
    public function newAction(Request $request)
    {
        $model = new NewsModel();
        $form = $this->createForm(NewsType::class, $model, [
            'action' => $this->generateUrl('news_new'),
        ]);

        if ($form->handleRequest($request)->isValid()) {
            $entity = new News($this->getUser(), $model->title, $model->content, $model->internal);
            if ($model->published) {
                $entity->publish();
            }
            $this->newsRepository->save($entity);

            return $this->redirectToRoute('news_show', ['slug' => $entity->getSlug()]);
        }

        return $this->render('news/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/news/{slug}", name="news_show")
     */
    public function showAction(News $news)
    {
        return $this->render('news/show.html.twig', [
            'news' => $news,
        ]);
    }

    /**
     * @Route("/news/{slug}/edit", name="news_edit")
     *
     * @Security("is_granted(constant('App\\Roles::NEWS_WRITER'))")
     */
    public function editAction(Request $request, News $news)
    {
        $model = NewsModel::fromEntity($news);
        $form = $this->createForm(NewsType::class, $model, [
            'action' => $this->generateUrl('news_edit', ['slug' => $news->getSlug()]),
        ]);

        if ($form->handleRequest($request)->isValid()) {
            $news->update($model->title, $model->content, $model->internal);

            if ($model->published) {
                $news->publish();
            } else {
                $news->unPublish();
            }

            $this->newsRepository->save($news);

            return $this->redirectToRoute('news_show', ['slug' => $news->getSlug()]);
        }

        return $this->render('news/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
