<?php

declare(strict_types=1);

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class DocumentsController extends Controller
{
    /**
     * @Route("/documents", name="documents_index")
     */
    public function indexAction(): Response
    {
        return $this->render('documents/index.html.twig');
    }
}
