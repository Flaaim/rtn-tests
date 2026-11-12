<?php

declare(strict_types=1);

namespace App\Course\Service\FileRenderer;

use RuntimeException;
use Sensiolabs\GotenbergBundle\GotenbergPdfInterface;

final readonly class AnswersToPDFRenderer implements FileRendererInterface
{
    public const string TEMPLATE = 'course/answers.html.twig';

    public function __construct(
        private GotenbergPdfInterface $gotenbergPdf,
    ) {}

    public function render(array $questions, string $courseId): string
    {
        $response = $this->gotenbergPdf
            ->html()
            ->content(self::TEMPLATE, [
                'questions' => $questions,
                'courseId' => $courseId,
            ])
            ->generate()->stream();

        ob_start();
        $response->sendContent();
        $result = ob_get_clean();

        if (false === $result || '' === $result) {
            throw new RuntimeException('Could not generate answers template');
        }

        return $result;
    }
}
