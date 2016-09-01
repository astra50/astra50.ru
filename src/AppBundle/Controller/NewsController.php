<?php

namespace AppBundle\Controller;

use AppBundle\Entity\News;
use AppBundle\Form\NewsType;
use AppBundle\Model\NewsModel;
use AppBundle\Repository\NewsRepository;
use Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route(service="app.controller.news")
 *
 * @author Konstantin Grachev <me@grachevko.ru>
 */
class NewsController extends Controller
{
    const NEWS_PER_PAGE = 3;

    /**
     * @var NewsRepository
     */
    private $newsRepository;

    /**
     * @param NewsRepository $newsRepository
     */
    public function __construct(NewsRepository $newsRepository)
    {
        $this->newsRepository = $newsRepository;
    }

    /**
     * @Route("/", name="news_list")
     */
    public function listAction(Request $request)
    {
        $news = $this->newsRepository->getLatestPaginated(!$this->isGranted(\AppRoles::NEWS_WRITER), !$this->isGranted(\AppRoles::COMMUNITY))
            ->setMaxPerPage(self::NEWS_PER_PAGE)
            ->setCurrentPage($request->query->get('page', 1));

        return $this->render('news/list.html.twig', [
            'pagerfanta' => $news,
        ]);
    }

    /**
     * @Route("/news/new", name="news_new")
     *
     * @Security("is_granted(constant('AppRoles::NEWS_WRITER'))")
     */
    public function newAction(Request $request)
    {
        $model = new NewsModel();
        $form = $this->createForm(NewsType::class, $model, [
            'action' => $this->generateUrl('news_new'),
        ]);

        if ($form->handleRequest($request)->isValid()) {
            $entity = new News(Uuid::create(), $this->getUser(), $model->title, $model->content, $model->internal);
            if ($model->published) {
                $entity->publish();
            }
            $this->newsRepository->save($entity);

            return $this->redirectToRoute('news_show', ['slug' => $entity->getSlug()]);
        }

        return $this->render(':news:edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/news/{slug}", name="news_show")
     */
    public function showAction(News $news)
    {
        return $this->render(':news:show.html.twig', [
            'news' => $news,
        ]);
    }

    /**
     * @Route("/news/{slug}/edit", name="news_edit")
     *
     * @Security("is_granted(constant('AppRoles::NEWS_WRITER'))")
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

        return $this->render(':news:edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
