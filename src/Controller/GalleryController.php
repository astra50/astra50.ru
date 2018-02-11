<?php

declare(strict_types=1);

namespace App\Controller;

use App\VK\Sections\Photos;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @Route("/gallery")
 */
final class GalleryController extends Controller
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
        $photos = $this->photos->get(getenv('VK_GALLERY_OWNER'), getenv('VK_GALLERY_ALBUM'));

        return $this->render('gallery/index.html.twig', [
            'photos' => $photos,
        ]);
    }
}
