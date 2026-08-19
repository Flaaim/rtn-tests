<?php

declare(strict_types=1);

namespace App\Testing\Service;

use App\Testing\Entity\Test\DTO\TicketDTO;
use App\Testing\Entity\Test\Settings;
use DomainException;

final class TicketGenerator
{
    /**
     * @param array<int, string> $allQuestionIds
     * @return TicketDTO[]
     */
    public function generate(Settings $settings, array $allQuestionIds): array
    {
        $totalQuestions = \count($allQuestionIds);
        $questionsPerTicket = $settings->getNumberQuestionsInTicket();
        $numberOfTickets = $settings->getNumberOfTickets();

        // Ситуация 1: Физически невозможно собрать даже один билет
        if ($questionsPerTicket > $totalQuestions) {
            throw new DomainException(\sprintf(
                'Not enough unique questions. Requested %d per ticket, but only %d available in courses.',
                $questionsPerTicket,
                $totalQuestions
            ));
        }

        $tickets = [];
        $bag = $allQuestionIds;
        shuffle($bag);

        for ($i = 0; $i < $numberOfTickets; ++$i) {
            // Проверяем, нужна ли "дозаправка" мешка для текущего билета
            if (\count($bag) < $questionsPerTicket) {
                // Забираем остатки из мешка
                $ticketQuestions = $bag;
                $needed = $questionsPerTicket - \count($ticketQuestions);

                // Заполняем мешок заново и перемешиваем
                $bag = $allQuestionIds;
                shuffle($bag);

                // ❗️ КРИТИЧНО: Убираем из нового мешка те вопросы, которые уже лежат в текущем билете
                $bag = array_values(array_diff($bag, $ticketQuestions));

                $additional = array_splice($bag, 0, $needed);
                $ticketQuestions = array_merge($ticketQuestions, $additional);
            } else {
                // Если в мешке хватает вопросов, просто отрезаем нужный кусок
                $ticketQuestions = array_splice($bag, 0, $questionsPerTicket);
            }

            $tickets[] = new TicketDTO($i + 1, $ticketQuestions);
        }

        return $tickets;
    }
}
