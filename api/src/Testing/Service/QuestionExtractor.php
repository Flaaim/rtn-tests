<?php

declare(strict_types=1);

namespace App\Testing\Service;

use App\Testing\Entity\Question;
use DomainException;

final class QuestionExtractor
{
    public function extract(string $draft): array
    {
        $draft_decoded = json_decode($draft, true, flags: JSON_THROW_ON_ERROR);
        if (null === $draft_decoded) {
            throw new DomainException('Draft data is not valid');
        }
        return array_map(static fn (array $question) => Question::fromDraft($question), $draft_decoded);
    }
}
