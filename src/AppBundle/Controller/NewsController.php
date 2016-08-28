<?php

namespace AppBundle\Controller;

use AppBundle\Entity\News;
use AppBundle\Form\NewsType;
use AppBundle\Model\NewsModel;
use AppBundle\Repository\NewsRepository;
use Ramsey\Uuid\Uuid;
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
        $news = $this->newsRepository->getLatestPaginated(!$this->isGranted(\Roles::NEWS_WRITER), !$this->isGranted(\Roles::COMMUNITY))
            ->setMaxPerPage(self::NEWS_PER_PAGE)
            ->setCurrentPage($request->query->get('page', 1))
        ;

        return $this->render('news/list.html.twig', [
            'pagerfanta' => $news,
        ]);
    }

    /**
     * @Route("/news/new", name="news_new")
     *
     * @Security("is_granted(constant('Roles::NEWS_WRITER'))")
     */
    public function newAction(Request $request)
    {
        $model = new NewsModel();
        $form = $this->createEditForm($model);

        if ($form->handleRequest($request)->isValid()) {
            $entity = $this->newsRepository->create(Uuid::uuid4(), $this->getUser(), $model->title, $model->content, $model->published, $model->internal);
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

    private function createEditForm(NewsModel $model)
    {
        return $this->createForm(NewsType::class, $model, [
            'action' => $this->generateUrl('news_new'),
        ]);
    }
}