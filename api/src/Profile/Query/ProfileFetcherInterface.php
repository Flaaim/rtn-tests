<?php

declare(strict_types=1);

namespace App\Profile\Query;

interface ProfileFetcherInterface
{
    public function getProfile(string $id): array;

    public function getUsers(int $page, int $limit): array;

    public function getFullProfile(string $id): array;

    public function getLookupList(): array;
}
