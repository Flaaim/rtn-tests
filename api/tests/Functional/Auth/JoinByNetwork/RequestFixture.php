<?php

declare(strict_types=1);

namespace Tests\Functional\Auth\JoinByNetwork;

use App\Auth\Entity\User\Email;
use App\Auth\Test\Builder\UserBuilder;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;

final class RequestFixture extends AbstractFixture
{
    public const array JOIN_BY_GOOGLE = [
        'email' => 'test@gmail.com',
        'network' => 'google',
        'identity' => '00001',
    ];

    public const array JOIN_BY_YANDEX = [
        'email' => 'test@yandex.ru',
        'network' => 'yandex',
        'identity' => '00003',
    ];
    public const string EXISTS_EMAIL = 'exists@email.com';
    public const string GUEST_ID = 'e92fa0c3-beb1-4fa6-bd85-fcc6a8dba182';

    public function load(ObjectManager $manager): void
    {
        $userWithGoogle = new UserBuilder()
            ->withEmail(new Email(self::JOIN_BY_GOOGLE['email']))
            ->viaNetwork(self::JOIN_BY_GOOGLE['network'], self::JOIN_BY_GOOGLE['identity'])
            ->build();

        $manager->persist($userWithGoogle);

        $userByEmail = new UserBuilder()
            ->withEmail(new Email(self::EXISTS_EMAIL))
            ->active()
            ->build();

        $manager->persist($userByEmail);

        $manager->flush();
    }
}
