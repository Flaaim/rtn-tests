<?php

declare(strict_types=1);

namespace App\Parser\Command\CreateParser;

use App\Infrastructure\Doctrine\Flusher;
use App\Parser\Entity\Parser\Cookie;
use App\Parser\Entity\Parser\Host;
use App\Parser\Entity\Parser\Parser;
use App\Parser\Entity\Parser\ParserId;
use App\Parser\Entity\Parser\ParserRepository;
use App\Parser\Service\CookieAuthParser;


final class Handler
{
    public function __construct(
        private readonly Flusher          $flusher,
        private readonly ParserRepository $parsers,
        private readonly CookieAuthParser $cookieParser,
    ) {}
    public function handle(Command $command): void
    {
        $host = new Host($command->host);

        if($this->parsers->hasByHost($host)) {
            throw new \DomainException('Parser with the same host already exists.');
        }

        $cookieFromResponse = $this->cookieParser->fetch($host, $command->login, $command->password);

        $cookie = new Cookie($cookieFromResponse);

        $parser = new Parser(
            ParserId::generate(),
            $host,
            $cookie
        );

        $this->parsers->add($parser);

        $this->flusher->flush();
    }
}
