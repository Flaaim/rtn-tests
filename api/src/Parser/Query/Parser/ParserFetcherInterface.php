<?php

namespace App\Parser\Query\Parser;

interface ParserFetcherInterface
{
    public function getOneById(string $id): array;

    public function hasOneById(string $id): bool;
}
