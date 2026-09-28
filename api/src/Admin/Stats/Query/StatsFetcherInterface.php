<?php

declare(strict_types=1);

namespace App\Admin\Stats\Query;

interface StatsFetcherInterface
{
    public function getUserStats(): ?array;
}
