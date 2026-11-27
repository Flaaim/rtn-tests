<?php

declare(strict_types=1);

namespace Tests\Functional\Admin\CourseAnswers\Recipient\Remove;

use App\CourseAnswers\Entity\Recipient\Email;
use App\CourseAnswers\Entity\Recipient\RecipientId;
use App\CourseAnswers\Test\Builder\RecipientBuilder;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class RequestFixture extends AbstractFixture implements DependentFixtureInterface
{
    public const string PRODUCT_ID = 'b0cf090c-8f57-443b-ba21-0d88b94eaa1c';
    public const string RECIPIENT_ID = '2efeadd8-53c8-45df-8193-bc5f327b8dee';
    public const string RECIPIENT_EMAIL = 'test@mail.ru';
    public const string RECIPIENT_NOT_FOUND_ID = '52382aac-cba9-49a6-932b-14634c453849';

    public function load(ObjectManager $manager): void
    {
        $recipient = new RecipientBuilder()
            ->withEmail(new Email(self::RECIPIENT_EMAIL))
            ->withProductId(self::PRODUCT_ID)
            ->withRecipientId(new RecipientId(self::RECIPIENT_ID))
            ->sent()
            ->build();
        $manager->persist($recipient);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            RoleFixture::class,
        ];
    }
}
