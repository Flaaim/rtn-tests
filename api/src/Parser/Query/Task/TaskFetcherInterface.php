<?php

declare(strict_types=1);

namespace App\Parser\Query\Task;

interface TaskFetcherInterface
{
    public function getOneById(string $id): array;
}
