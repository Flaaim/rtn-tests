<?php

declare(strict_types=1);

namespace App\Parser\Entity\Parser;

use App\Parser\Event\ParserCreated;
use App\SharedDomain\AggregateRoot;
use App\SharedDomain\Event\EventTrait;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'parsers')]
final class Parser implements AggregateRoot
{
    use EventTrait;

    public function __construct(
        #[ORM\Id]
        #[ORM\Column(type: 'parser_id')]
        private ParserId $id,
        #[ORM\Column(type: 'parser_host', unique: true)]
        private Host     $host,
        #[ORM\Column(type: 'text')]
        private string   $cookie,
        #[ORM\Embedded(class: Credentials::class, columnPrefix: false)]
        private Credentials $credentials,
    )
    {
        $this->recordEvent(new ParserCreated($this->host->getValue()));
    }

    public function getId(): ParserId
    {
        return $this->id;
    }

    public function getHost(): Host
    {
        return $this->host;
    }

    public function getCookie(): string
    {
        return $this->cookie;
    }

    public function refreshAuth(string $cookie): void
    {
        $this->cookie = $cookie;
    }

    public function getCredentials(): Credentials
    {
        return $this->credentials;
    }
}
