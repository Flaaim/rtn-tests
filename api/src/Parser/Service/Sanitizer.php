<?php

declare(strict_types=1);

namespace App\Parser\Service;

final class Sanitizer implements SanitizerInterface
{
    public function cleanTextContent(string $content): string
    {
        $cleaned = strip_tags($content, '<br>');

        // Заменяем <br> на переносы строк
        $cleaned = str_replace(['<br>', '<br/>', '<br />'], "\n", $cleaned);

        // Убираем лишние пробелы и переносы
        $cleaned = preg_replace('/\s+/', ' ', $cleaned);

        // Убираем дефисы в начале и конце
        $cleaned = preg_replace('/^-|-$/', '', $cleaned);

        return trim($cleaned);
    }
}
