<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Testing\Test\GetCompetencyMatrix;

use App\Infrastructure\Http\Validator\Validator;
use App\Testing\Query\Test\GetCompetencyMatrix\Query;
use App\Testing\Query\Test\GetCompetencyMatrix\QueryHandler;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final readonly class RequestAction
{
    public function __construct(
        private QueryHandler $queryHandler,
        private Validator $validator,
        private Security $security,
    ) {}

    #[Route('/v1/testing/competency-matrix', name: 'competency.matrix', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function __invoke(): Response
    {
        $user = $this->security->getUser();
        if (null === $user) {
            return new JsonResponse(null, Response::HTTP_UNAUTHORIZED);
        }
        $userId = $user->getUserIdentifier();

        $query = new Query($userId);

        $this->validator->validate($query);

        $result = $this->queryHandler->handle($query);

        return new JsonResponse($result, Response::HTTP_OK);
    }
}
