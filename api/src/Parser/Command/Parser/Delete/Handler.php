<?php

declare(strict_types=1);

namespace App\Parser\Command\Parser\Delete;

use App\Infrastructure\Doctrine\Flusher;
use App\Parser\Entity\Parser\ParserId;
use App\Parser\Entity\Parser\ParserRepository;
use DomainException;

final class Handler
{
    public function __construct(
        private readonly ParserRepository $parsers,
        private readonly Flusher $flusher,
    ) {}

    public function handle(Command $command): void
    {
        $parser = $this->parsers->find(new ParserId($command->id));
        if (null === $parser) {
            throw new DomainException('Parser not found.');
        }

        $this->parsers->remove($parser);

        $this->flusher->flush();
    }
}
