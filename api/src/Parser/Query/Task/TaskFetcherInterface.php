<?php

namespace App\Parser\Query\Task;

interface TaskFetcherInterface
{
    public function getOneById(string $id): array;
}
