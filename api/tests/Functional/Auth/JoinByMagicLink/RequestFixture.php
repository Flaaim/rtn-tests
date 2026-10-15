<?php

declare(strict_types=1);

namespace Tests\Functional\Auth\JoinByMagicLink;

use App\Auth\Entity\User\Email;
use App\Auth\Test\Builder\UserBuilder;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;

final class RequestFixture extends AbstractFixture
{
    public const string USER_EMAIL = 'guest@app.ru';
    public const string EXISTS_EMAIL = 'exists@app.ru';

    public function load(ObjectManager $manager): void
    {
        $user = new UserBuilder()
            ->withEmail(new Email(self::EXISTS_EMAIL))
            ->build();
        $manager->persist($user);

        $manager->flush();
    }
}
