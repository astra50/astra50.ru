<?php

declare(strict_types=1);

namespace App\Controller;

use App\VK\Sections\Photos;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * @Route("/gallery")
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
        $photos = $this->photos->get(getenv('VK_GALLERY_OWNER'), getenv('VK_GALLERY_ALBUM'));

        return $this->render('gallery/index.html.twig', [
            'photos' => $photos,
        ]);
    }
}
