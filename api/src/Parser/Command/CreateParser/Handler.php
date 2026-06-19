<?php

declare(strict_types=1);

namespace App\Parser\Command\CreateParser;

use App\Infrastructure\Doctrine\Flusher;
use App\Parser\Entity\Parser\Course;
use App\Parser\Entity\Parser\Parser;
use App\Parser\Entity\Parser\ParserId;
use App\Parser\Entity\Parser\ParserRepository;


final class Handler
{
    public function __construct(
        private readonly Flusher $flusher,
        private readonly ParserRepository $parsers,
    ) {}
    public function handle(Command $command): void
    {
        $parser = new Parser(
            ParserId::generate(),
            $command->host,
            new Course($command->data),
            new Cookie()
        );

        $this->parsers->add($parser);

        $this->flusher->flush();
    }
}
