<?php

declare(strict_types=1);

namespace App\Parser\Entity\Parser;

use App\Parser\Event\ParserCreated;
use App\SharedDomain\AggregateRoot;
use App\SharedDomain\Event\EventTrait;

final class Parser implements AggregateRoot
{
    use EventTrait;
    public function __construct(
        private ParserId $id,
        private Host $host,
        private Cookie  $cookie
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
