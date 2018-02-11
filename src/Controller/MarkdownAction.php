<?php

declare(strict_types=1);

namespace App\Controller;

use App\Markdown\MarkdownInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class MarkdownAction
{
    /**
     * @Route("/markdown", name="markdown")
     */
    public function __invoke(Request $request, MarkdownInterface $markdown): JsonResponse
    {
        try {
            $content = $request->isMethod('POST')
                ? $request->request->get('markdown')
                : $request->query->get('markdown');
        } catch (\Throwable $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        return new JsonResponse(['html' => $markdown->toHtml($content)]);
    }
}
