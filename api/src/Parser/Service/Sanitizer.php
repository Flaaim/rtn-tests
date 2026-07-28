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


    public function extractImagesFromContent(string $content, string $host): string
    {
        $images = [];

        if (preg_match_all('/<img[^>]+src="\/([^"]+)"[^>]*>/', $content, $matches)) {
            foreach ($matches[0] as $index => $imgTag) {
                $imagePath = $matches[1][$index];
                $absoluteUrl = $host . DIRECTORY_SEPARATOR . $imagePath;
                $images[] = $absoluteUrl;
            }
        }

        return implode(' ', $images);
    }
}
