<?php

declare(strict_types=1);

namespace App\Subscription\Interfaces;

use App\Subscription\Interfaces\DTO\ProfileDTO;

interface ProfileInterface
{
    public function getDetail(string $userId): ProfileDTO;
}
