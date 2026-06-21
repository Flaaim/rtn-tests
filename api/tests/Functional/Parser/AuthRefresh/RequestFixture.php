<?php

declare(strict_types=1);

namespace Tests\Functional\Parser\AuthRefresh;

use App\Parser\Entity\Parser\Credentials;
use App\Parser\Entity\Parser\Host;
use App\Parser\Entity\Parser\Parser;
use App\Parser\Entity\Parser\ParserId;
use App\Parser\Service\Encrypt\EncryptService;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;

final class RequestFixture extends AbstractFixture
{
    public const string PARSER_ID = '5134bc29-ef64-414f-a0d4-b1cf0166c7e2';
    public const string PARSER_NOT_FOUND_ID = '5a460825-a8da-4bcc-84d0-144180935c7e';
    public function load(ObjectManager $manager): void
    {
        $encrypter = $this->getEncrypter();

        $parser = new Parser(
            new ParserId(self::PARSER_ID),
            new Host('https://example.com'),
            'some sookie',
            new Credentials($encrypter->encrypt('login'), $encrypter->encrypt('password')),
        );

        $manager->persist($parser);

        $manager->flush();
    }

    private function getEncrypter(): EncryptService
    {
        return new EncryptService('secret');
    }
}
