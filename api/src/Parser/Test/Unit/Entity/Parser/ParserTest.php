<?php

declare(strict_types=1);

namespace App\Parser\Test\Unit\Entity\Parser;

use App\Parser\Entity\Parser\Cookie;
use App\Parser\Entity\Parser\Host;
use App\Parser\Entity\Parser\Parser;
use App\Parser\Entity\Parser\ParserId;
use App\Parser\Event\ParserCreated;
use PHPUnit\Framework\TestCase;

final class ParserTest extends TestCase
{
    public function testCreate(): void
    {
        $parser = new Parser(
            $id = ParserId::generate(),
            $host = new Host('http://example.com/'),
            $cookie = new Cookie(['zero', 'one', 'two', 'three']),
        );

        self::assertEquals($id->getValue(), $parser->getId()->getValue());
        self::assertEquals($host->getValue(), $parser->getHost()->getValue());

        $events = $parser->releaseEvents();

        $event = end($events);

        self::assertInstanceOf(ParserCreated::class, $event);
        self::assertEquals($host->getValue(), $event->host);
    }
}
