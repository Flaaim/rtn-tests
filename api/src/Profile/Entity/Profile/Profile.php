<?php

declare(strict_types=1);

namespace App\Profile\Entity\Profile;

use App\Profile\Event\ProfileAdded;
use App\Profile\Event\ProfileRemoved;
use App\SharedDomain\AggregateRoot;
use App\SharedDomain\Event\EventTrait;
use Doctrine\ORM\Mapping as ORM;
use DomainException;

#[ORM\Entity]
#[ORM\Table(name: 'profiles')]
final class Profile implements AggregateRoot
{
    use EventTrait;

    private function __construct(
        #[ORM\Id]
        #[ORM\Column(type: 'profile_id', unique: true)]
        private ProfileId $id,
        #[ORM\Column(type: 'profile_email')]
        private Email $email,
        #[ORM\Column(type: 'profile_role')]
        private Role $role,
        #[ORM\Column(type: 'profile_status')]
        private Status $status,
        #[ORM\Column(type: 'string', length: 55, nullable: true)]
        private ?string $name = null,
        #[ORM\Column(type: 'string', length: 75, nullable: true)]
        private ?string $surname = null
    ) {}

    public static function createManually(
        ProfileId $profileId,
        Email $email,
        Role $role,
        ?string $name = null,
        ?string $surname = null,
    ): self {
        $profile = new self(
            $profileId,
            $email,
            $role,
            Status::ok(),
            $name,
            $surname
        );

        $profile->recordEvent(new ProfileAdded(
            $profile->getId()->getValue(),
            $profile->getEmail()->getValue(),
            $profile->getRole()->getName()
        ));
        return $profile;
    }

    public static function createFromSignUp(
        ProfileId $profileId,
        Email $email,
        Role $role,
    ): self {
        return new self(
            $profileId,
            $email,
            $role,
            Status::ok(),
        );
    }

    public function getId(): ProfileId
    {
        return $this->id;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getRole(): Role
    {
        return $this->role;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getStatus(): Status
    {
        return $this->status;
    }

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function remove(): void
    {
        if ($this->role->isAdmin()) {
            throw new DomainException('Cannot remove admin profile.');
        }
        $this->recordEvent(new ProfileRemoved($this->id->getValue()));
    }

    public function getAggregateId(): string
    {
        return $this->id->getValue();
    }
}
