<?php

declare(strict_types=1);

namespace App\Parser\Service;

use DomainException;

final class Sanitizer implements SanitizerInterface
{
    public function cleanTextContent(string $content): string
    {
        $cleaned = strip_tags($content, '<br>');

        // Заменяем <br> на переносы строк
        $cleaned = str_replace(['<br>', '<br/>', '<br />'], "\n", $cleaned);

        // Убираем лишние пробелы и переносы
        $cleaned = preg_replace('/\s+/', ' ', $cleaned);
        if (null === $cleaned) {
            throw new DomainException('Cleaned text content cannot be null.');
        }
        // Убираем дефисы в начале и конце
        $cleaned = preg_replace('/^-|-$/', '', $cleaned);
        if (null === $cleaned) {
            throw new DomainException('Cleaned text content cannot be null.');
        }
        return trim($cleaned);
    }

    public function extractImgFromAnswerText(string $content, string $host): string
    {
        $images = [];

        if (false !== preg_match_all('/<img[^>]+src="\/([^"]+)"[^>]*>/', $content, $matches)) {
            foreach (array_keys($matches[0]) as $index) {
                $imagePath = $matches[1][$index];
                $absoluteUrl = $host . \DIRECTORY_SEPARATOR . $imagePath;
                $images[] = $absoluteUrl;
            }
        }

        return implode(' ', $images);
    }

    public function extractImgFromQuestionMainImg(string $content, string $host): string
    {
        if (empty($content) || !str_contains($content, '<img')) {
            return '';
        }

        // Извлекаем путь к изображению из HTML
        if (false !== preg_match('/src="\/([^"]+)"/', $content, $matches)) {
            return $host . \DIRECTORY_SEPARATOR . $matches[1];
        }

        return $content;
    }
}
