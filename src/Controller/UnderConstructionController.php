<?php

declare(strict_types=1);

namespace App\Controller;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class UnderConstructionController extends BaseController
{
    public function indexAction()
    {
        return $this->render('under_construction.html.twig');
    }
}
