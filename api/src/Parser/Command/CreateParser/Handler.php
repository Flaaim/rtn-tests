<?php

declare(strict_types=1);

namespace App\Parser\Command\CreateParser;

use App\Infrastructure\Doctrine\Flusher;
use App\Parser\Entity\Parser\Credentials;
use App\Parser\Entity\Parser\Host;
use App\Parser\Entity\Parser\Parser;
use App\Parser\Entity\Parser\ParserId;
use App\Parser\Entity\Parser\ParserRepository;
use App\Parser\Service\CookieAuthParser;
use App\Parser\Service\EncryptService;
use App\Parser\Service\GlueCookie;
use DomainException;

final class Handler
{
    public function __construct(
        private readonly Flusher $flusher,
        private readonly ParserRepository $parsers,
        private readonly CookieAuthParser $cookieParser,
        private readonly GlueCookie $glueCookie,
    ) {}

    public function handle(Command $command): void
    {
        $host = new Host($command->host);

        if ($this->parsers->hasByHost($host)) {
            throw new DomainException('Parser with the same host already exists.');
        }

        $cookieFromResponse = $this->cookieParser->fetch($host, $command->login, $command->password);

        $cookie = $this->glueCookie->glue($cookieFromResponse);

        $credentials = new Credentials(
          $this->encryptService->encrypt($command->login),
          $this->encryptService->encrypt($command->password),
        );

        $parser = new Parser(
            ParserId::generate(),
            $host,
            $cookie,
            $credentials
        );

        $this->parsers->add($parser);

        $this->flusher->flush();
    }
}
