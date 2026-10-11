<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Testing\Attempt\Launch;

use App\Infrastructure\Http\Validator\Validator;
use App\Testing\Command\Attempt\DemoLaunch\Command as DemoCommand;
use App\Testing\Command\Attempt\Launch\Command as UserCommand;
use RuntimeException;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Attribute\Route;

final readonly class RequestAction
{
    public function __construct(
        private MessageBusInterface $messageBus,
        private Validator $validator,
        private Security $security
    ) {}

    #[Route('/v1/testing/attempts', name: 'testing.attempt.launch', methods: ['POST'])]
    public function __invoke(Request $request): Response
    {
        $userId = $this->security->getUser()?->getUserIdentifier();
        $guestId = $request->headers->get('X-Guest-ID');

        $body = $request->toArray();

        $testId = $body['testId'] ?? '';
        $ticketNumber = (int)($body['ticketNumber'] ?? 0);

        if (null !== $userId) {
            $command = new UserCommand($testId, $userId, $ticketNumber);
        } elseif (null !== $guestId) {
            $command = new DemoCommand($testId, $guestId, $ticketNumber);
        } else {
            return new JsonResponse(['error' => 'No identity provided'], Response::HTTP_BAD_REQUEST);
        }

        $this->validator->validate($command);

        $envelope = $this->messageBus->dispatch($command);
        $stamp = $envelope->last(HandledStamp::class);

        if (null === $stamp) {
            throw new RuntimeException('Could not handle message');
        }

        $attemptId = $stamp->getResult();

        return new JsonResponse(['attemptId' => $attemptId], Response::HTTP_CREATED);
    }
}
