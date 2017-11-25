<?php

declare(strict_types=1);

namespace App\Controller;

use App\Markdown\MarkdownInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class MarkdownAction
{
    /**
     * @var MarkdownInterface
     */
    private $markdown;

    public function __construct(MarkdownInterface $markdown)
    {
        $this->markdown = $markdown;
    }

    /**
     * @Route("/markdown", name="markdown")
     */
    public function __invoke(Request $request): JsonResponse
    {
        try {
            $content = $request->isMethod('POST')
                ? $request->request->get('markdown')
                : $request->query->get('markdown');
        } catch (\Throwable $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        return new JsonResponse(['html' => $this->markdown->toHtml($content)]);
    }
}
