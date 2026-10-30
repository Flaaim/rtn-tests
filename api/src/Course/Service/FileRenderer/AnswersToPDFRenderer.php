<?php

declare(strict_types=1);

namespace App\Course\Service\FileRenderer;

use Nucleos\DompdfBundle\Wrapper\DompdfWrapperInterface;
use Twig\Environment;

final readonly class AnswersToPDFRenderer implements FileRendererInterface
{
    public const string TEMPLATE = 'course/answers.html.twig';

    public function __construct(
        private Environment $twig,
        private DompdfWrapperInterface $wrapper,
    ) {}

    public function render(array $questions, string $courseId): string
    {
        $content = $this->twig->render(self::TEMPLATE, ['questions' => $questions]);

        return $this->wrapper->getPdf($content);
    }
}
