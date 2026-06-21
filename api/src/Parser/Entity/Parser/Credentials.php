<?php

declare(strict_types=1);

namespace App\Parser\Entity\Parser;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
final class Credentials
{
    public function __construct(
        #[ORM\Column(type: 'string', length: 255)]
       private string $login,
       #[ORM\Column(type: 'string', length: 255)]
       private string $password,
    ) {
    }

    public function getLogin(): string
    {
        return $this->login;
    }
    public function getPassword(): string
    {
        return $this->password;
    }
}
