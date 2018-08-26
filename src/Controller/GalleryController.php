<?php

declare(strict_types=1);

namespace App\Controller;

use App\VK\Sections\Photos;
use LogicException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

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
    public function indexAction(): Response
    {
        $owner = getenv('VK_GALLERY_OWNER');
        $album = getenv('VK_GALLERY_ALBUM');

        if (false === $owner || false === $album) {
            throw new LogicException('Gallery credentials not defined.');
        }

        $photos = $this->photos->get($owner, $album);

        return $this->render('gallery/index.html.twig', [
            'photos' => $photos,
        ]);
    }
}
