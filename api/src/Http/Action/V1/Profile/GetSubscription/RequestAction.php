<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Profile\GetSubscription;

use App\Infrastructure\Http\Validator\Validator;
use App\Subscription\Query\GetSubscription\Query;
use App\Subscription\Query\GetSubscription\QueryHandler;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final readonly class RequestAction
{

    public function __construct(
        private QueryHandler $handler,
        private Validator $validator,
        private Security $security,
    ) {}
    #[Route('/v1/me/subscriptions', name: 'subscription.get', methods: ['GET'])]
    public function __invoke(): Response
    {
        $user = $this->security->getUser();
        if (null === $user) {
            return new JsonResponse(null, Response::HTTP_UNAUTHORIZED);
        }
        $userId = $user->getUserIdentifier();

        $query = new Query($userId);

        $this->validator->validate($query);

        $result = $this->handler->handle($query);

        return new JsonResponse($result, Response::HTTP_OK);
    }
}
