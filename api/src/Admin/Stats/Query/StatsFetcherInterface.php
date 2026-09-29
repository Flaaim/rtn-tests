<?php

declare(strict_types=1);

namespace App\Admin\Stats\Query;

interface StatsFetcherInterface
{
    public function getUsersStats(): ?array;

    public function getSubscriptionsStats(): ?array;
}
