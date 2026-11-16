<?php

declare(strict_types=1);

namespace App\Profile\Test\Builder;

use App\Profile\Entity\Profile\Email;
use App\Profile\Entity\Profile\Profile;
use App\Profile\Entity\Profile\ProfileId;
use App\Profile\Entity\Profile\Role;

final class ProfileBuilder
{
    private ProfileId $id;
    private Email $email;
    private Role $role;
    private bool $banned = false;

    /** @psalm-suppress PossiblyUnusedMethod */
    public function __construct()
    {
        $this->id = ProfileId::generate();
        $this->email = new Email('mail@example.com');
        $this->role = Role::user();
    }

    /** @psalm-suppress PossiblyUnusedMethod */
    public function withProfileId(ProfileId $id): self
    {
        $clone = clone $this;
        $clone->id = $id;
        return $clone;
    }

    /** @psalm-suppress PossiblyUnusedMethod */
    public function withRole(Role $role): self
    {
        $clone = clone $this;
        $clone->role = $role;
        return $clone;
    }

    /** @psalm-suppress PossiblyUnusedMethod */
    public function withEmail(Email $email): self
    {
        $clone = clone $this;
        $clone->email = $email;
        return $clone;
    }

    /** @psalm-suppress PossiblyUnusedMethod */
    public function banned(): self
    {
        $clone = clone $this;
        $this->banned = true;
        return $clone;
    }

    /** @psalm-suppress PossiblyUnusedMethod */
    public function build(): Profile
    {
        $profile = Profile::createManually(
            $this->id,
            $this->email,
            $this->role,
        );

        if ($this->banned) {
            $profile->banned();
        }

        return $profile;
    }
}
