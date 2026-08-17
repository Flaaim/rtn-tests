<?php

declare(strict_types=1);

namespace Tests\Functional\Admin\Testing\Test\ChangeCipher;

use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Role;
use App\Auth\Test\Builder\UserBuilder;
use App\Testing\Entity\Test\TestId;
use App\Testing\Test\Builder\TestBuilder;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;

final class RequestFixture extends AbstractFixture
{
    public const string TEST_ID = 'ff1f1a44-f5f5-4956-9a09-feef0d28cedd';
    public const string TEST_NOT_FOUND_ID = '96861841-ad71-467d-b32b-2f0e539de742';
    public const string TEST_ACTIVE_ID = '958467b2-37c1-4301-bb69-df3325aff49c';

    public const string TEST_NAME = 'Первая помощь';
    public const string TEST_CIPHER = 'ОТ 201.18';
    public const string TEST_CIPHER_ACTIVE = 'ПБ 115.26';

    public const string TEST_SLUG = 'ot-201';
    public const string TEST_SLUG_ACTIVE = 'pb-115';

    public const string ADMIN_EMAIL = 'admin@mail.ru';
    public const string ADMIN_PASSWORD = 'admin';

    public const string USER_EMAIL = 'user@mail.ru';
    public const string USER_PASSWORD = 'user';

    public function load(ObjectManager $manager): void
    {
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

        $test = new TestBuilder()
            ->withId(new TestId(self::TEST_ID))
            ->withName(self::TEST_NAME)
            ->withCipher(self::TEST_CIPHER)
            ->withSlug(self::TEST_SLUG)
            ->build();
        $manager->persist($test);

        $testActive = new TestBuilder()
            ->withId(new TestId(self::TEST_ACTIVE_ID))
            ->withName(self::TEST_NAME)
            ->withCipher(self::TEST_CIPHER_ACTIVE)
            ->withSlug(self::TEST_SLUG_ACTIVE)
            ->active()
            ->build();
        $manager->persist($testActive);

        $manager->flush();
    }
}
