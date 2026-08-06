<?php

declare(strict_types=1);

namespace Tests\Functional\Admin\Parser\LaunchParse;

use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Role;
use App\Auth\Test\Builder\UserBuilder;
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
    public const string PARSER_NOT_FOUND_ID = 'a91c2d90-06e0-47d8-a8f7-8ec2ffbf160e';

    public const string ADMIN_EMAIL = 'admin@mail.ru';
    public const string ADMIN_PASSWORD = 'admin';

    public const string USER_EMAIL = 'user@mail.ru';
    public const string USER_PASSWORD = 'user';

    public function load(ObjectManager $manager): void
    {
        $encrypter = $this->getEncrypter();

        $admin = new UserBuilder()
            ->withEmail(new Email(self::ADMIN_EMAIL))
            ->withPassword(self::ADMIN_PASSWORD)
            ->withRole(Role::admin())
            ->active()
            ->build();
        $manager->persist($admin);

        $user = new UserBuilder()
            ->withEmail(new Email(self::USER_EMAIL))
            ->withPassword(self::USER_PASSWORD)
            ->active()
            ->build();
        $manager->persist($user);

        $parser = new Parser(
            $parserId = new ParserId(self::PARSER_ID),
            new Host('https://example.com'),
            'some cookie',
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
