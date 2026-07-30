<?php

declare(strict_types=1);

namespace Tests\Functional\Parser\GetParsers;

use App\Parser\Entity\Parser\Credentials;
use App\Parser\Entity\Parser\Host;
use App\Parser\Entity\Parser\Parser;
use App\Parser\Entity\Parser\ParserId;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;

final class RequestFixture extends AbstractFixture
{
    public const string PARSER_ID = '5134bc29-ef64-414f-a0d4-b1cf0166c7e2';

    public function load(ObjectManager $manager): void
    {
        $parser = new Parser(
            new ParserId(self::PARSER_ID),
            new Host('https://example.com'),
            'some sookie',
            new Credentials('login', 'password'),
        );

        $manager->persist($parser);

        $manager->flush();
    }
}
