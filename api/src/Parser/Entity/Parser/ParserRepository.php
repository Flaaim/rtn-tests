<?php

declare(strict_types=1);

namespace App\Parser\Entity\Parser;

interface ParserRepository
{
    public function add(Parser $parser): void;
}
