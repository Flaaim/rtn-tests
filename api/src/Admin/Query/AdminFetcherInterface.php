<?php

declare(strict_types=1);

namespace App\Admin\Query;

interface AdminFetcherInterface
{
    public function getSubscriptionStats(): array;
}
