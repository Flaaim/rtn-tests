<?php

declare(strict_types=1);

namespace App\Testing\Command\Create;

use App\Course\Api\CourseApi;
use App\Testing\Entity\DTO\TicketDTO;

final class Handler
{
    public function __construct(
        private readonly CourseApi $courseApi,
    ) {}

    public function handle(Command $command): void
    {
        $allQuestionIds = $this->courseApi->getQuestionIds($command->courseId);

        shuffle($allQuestionIds);

        /** @var TicketDTO[] $tickets */
        $tickets = [];
        $chunks = array_chunk($allQuestionIds, $command->numberQuestionsInTicket);
        foreach ($chunks as $index => $chunk) {
            if ($index >= $command->numberOfTickets) {
                break;
            }

            $tickets[] = new TicketDTO(
                $index + 1,
                $chunk
            );
        }
    }
}
