<?php

namespace AppBundle\Controller;

use AppBundle\VK\Sections\Photos;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * @Route("/gallery", service="app.controller.gallery")
 *
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class GalleryController extends BaseController
{
    /**
     * @var Photos
     */
    private $photos;

    public function __construct(Photos $photos)
    {
        $this->photos = $photos;
    }

    /**
     * @Route(name="gallery_index")
     */
    public function indexAction()
    {
        $photos = $this->photos->get(
            $this->getParameter('vk_gallery_owner'),
            $this->getParameter('vk_gallery_album')
        );

        return $this->render('gallery/index.html.twig', [
            'photos' => $photos,
        ]);
    }
}
