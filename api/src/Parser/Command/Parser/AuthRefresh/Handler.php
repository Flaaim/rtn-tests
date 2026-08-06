<?php

declare(strict_types=1);

namespace App\Parser\Command\Parser\AuthRefresh;

use App\Infrastructure\Doctrine\Flusher;
use App\Parser\Entity\Parser\ParserId;
use App\Parser\Entity\Parser\ParserRepository;
use App\Parser\Service\Encrypt\EncryptInterface;
use App\Parser\Service\GlueCookie;
use App\Parser\Service\Parse\CookieAuthParser;
use DomainException;

final class Handler
{
    public function __construct(
        private readonly Flusher $flusher,
        private readonly ParserRepository $parsers,
        private readonly CookieAuthParser $cookieParser,
        private readonly GlueCookie $glueCookie,
        private readonly EncryptInterface $encryptService,
    ) {}

    public function handle(Command $command): void
    {
        $parser = $this->parsers->find(new ParserId($command->parserId));

        if (null === $parser) {
            throw new DomainException('Parser not found.');
        }

        $cookieFromResponse = $this->cookieParser->fetch(
            $parser->getHost(),
            $this->encryptService->decrypt($parser->getCredentials()->getLogin()),
            $this->encryptService->decrypt($parser->getCredentials()->getPassword())
        );

        $cookie = $this->glueCookie->glue($cookieFromResponse);

        $parser->refreshAuth($cookie);

        $this->flusher->flush();
    }
}
