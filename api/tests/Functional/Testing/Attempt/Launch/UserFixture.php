<?php

declare(strict_types=1);

namespace Tests\Functional\Testing\Attempt\Launch;

use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Id;
use App\Auth\Test\Builder\UserBuilder;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;

final class UserFixture extends AbstractFixture
{
    public const string USER_ID = '39bc2486-50b5-4207-a366-323389d21507';
    public const string USER_EMAIL = 'active@app.test';
    public const string USER_PASSWORD = 'user';

    public const string TRIAL_USER_ID = 'a100547d-396f-45e7-8442-fc9ad4aebdff';
    public const string TRIAL_USER_EMAIL = 'trial@app.test';

    public const string NEW_USER_ID = '15d8821d-3226-4f8d-9fb2-85c548c94119';
    public const string NEW_USER_EMAIL = 'new@app.test';

    public function load(ObjectManager $manager): void
    {
        $active = new UserBuilder()
            ->withId(new Id(self::USER_ID))
            ->withEmail(new Email(self::USER_EMAIL))
            ->withPassword(self::USER_PASSWORD)
            ->active()
            ->build();
        $manager->persist($active);

        $trial = new UserBuilder()
            ->withId(new Id(self::TRIAL_USER_ID))
            ->withEmail(new Email(self::TRIAL_USER_EMAIL))
            ->withPassword(self::USER_PASSWORD)
            ->active()
            ->build();
        $manager->persist($trial);

        $new = new UserBuilder()
            ->withId(new Id(self::NEW_USER_ID))
            ->withEmail(new Email(self::NEW_USER_EMAIL))
            ->withPassword(self::USER_PASSWORD)
            ->active()
            ->build();
        $manager->persist($new);

        $manager->flush();
    }
}
