<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\News;
use App\Form\Model\NewsModel;
use App\Form\Type\NewsType;
use App\Roles;
use Doctrine\ORM\EntityManagerInterface;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class NewsController extends Controller
{
    private const NEWS_PER_PAGE = 3;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/", name="news_index", defaults={"page": 1})
     * @Route("/page/{page}", requirements={"page": "[1-9]\d*"}, name="news_index_paginated")
     */
    public function indexAction(int $page)
    {
        $qb = $this->em->createQueryBuilder()
            ->select('entity')
            ->from(News::class, 'entity')
            ->join('entity.author', 'author')
            ->orderBy('entity.publishedAt', 'DESC');

        if (!$this->isGranted(Roles::NEWS_WRITER)) {
            $qb->where('entity.published = :published')
                ->setParameter('published', true);
        }

        if (!$this->isGranted(Roles::COMMUNITY)) {
            $qb->andWhere('entity.internal = :internal')
                ->setParameter('internal', false);
        }

        $paginator = new Pagerfanta(new DoctrineORMAdapter($qb, false));
        $paginator->setMaxPerPage(self::NEWS_PER_PAGE);
        $paginator->setCurrentPage($page);

        return $this->render('news/index.html.twig', [
            'news' => $paginator,
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

            $this->em->persist($entity);
            $this->em->flush();

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

            $this->em->flush();

            return $this->redirectToRoute('news_show', ['slug' => $news->getSlug()]);
        }

        return $this->render('news/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
