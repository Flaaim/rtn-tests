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
        private Host $host,
        #[ORM\Column(type: 'parser_cookie', length: 255)]
        private Cookie $cookie
    ) {
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

    public function getCookie(): Cookie
    {
        return $this->cookie;
    }
}
