<?php

declare(strict_types=1);

namespace App\OAuth\Entity;

use Symfony\Component\Security\Core\User\UserInterface;

final class UserAdapter implements UserInterface
{
    public function __construct(
        private readonly string $identifier,
        private readonly array $roles = ['ROLE_USER']
    ) {}

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function getUserIdentifier(): string
    {
        /** @var non-empty-string */
        return $this->identifier;
    }
}
