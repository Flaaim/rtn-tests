<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Course\GetQuestionsSitemap;

use App\Course\Query\Course\GetQuestionsSitemap\QueryHandler;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final readonly class RequestAction
{
    public function __construct(
        private QueryHandler $handler,
    ) {}

    #[Route('/v1/seo/sitemap/questions', name: 'seo.sitemap.questions', methods: ['GET'])]
    public function __invoke(): Response
    {
        $ids = $this->handler->handle();

        return new JsonResponse($ids, Response::HTTP_OK);
    }
}
