<?php

declare(strict_types=1);

namespace App\Testing\Query\Analytics;

interface AnalyticsFetcherInterface
{
    public function getPopularTests(string $userId): array;
}
