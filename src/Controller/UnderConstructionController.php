<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Response;

final class UnderConstructionController
{
    public function __invoke(EngineInterface $engine): Response
    {
        return $engine->renderResponse('under_construction.html.twig');
    }
}
