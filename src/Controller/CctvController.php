<?php

declare(strict_types=1);

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class CctvController extends BaseController
{
    /**
     * @Route("/cctv/", name="cctv_index")
     */
    public function indexAction()
    {
        return $this->render('cctv/index.html.twig');
    }
}
