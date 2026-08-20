<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Admin\Course\Course\GetQuestionsByCourseIds;

use App\Course\Query\Course\GetQuestionsByCourseIds\Query;
use App\Course\Query\Course\GetQuestionsByCourseIds\QueryHandler;
use App\Infrastructure\Http\Validator\Validator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class RequestAction
{
    public function __construct(
        private readonly QueryHandler $handler,
        private readonly Validator $validator
    ) {}

    #[Route('/v1/admin/testing/courses/questions', name: 'admin.testing.courses.get.questions', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function __invoke(Request $request): Response
    {
        $courseIds = $request->query->all('ids');

        $query = new Query($courseIds);

        $this->validator->validate($query);

        $questions = $this->handler->handle($query);

        return new JsonResponse($questions, Response::HTTP_OK);
    }
}
