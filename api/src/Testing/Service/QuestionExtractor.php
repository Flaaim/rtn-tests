<?php

declare(strict_types=1);

namespace App\Testing\Service;

use App\Testing\Entity\Question;

final class QuestionExtractor
{

    public function extract(string $draft): array
    {
        $draft_decoded = json_decode($draft, true, flags: JSON_THROW_ON_ERROR);
        if($draft_decoded === null){
            throw new \Exception('Draft data is not valid');
        }
        return array_map(function (array $question) {
            //$question['questionImg'] = $this->
            return Question::fromDraft($question);
        }, $draft_decoded);
    }
}
