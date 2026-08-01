<?php

declare(strict_types=1);

namespace Tests\Functional\Admin\Tasks\GetTask;

use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Role;
use App\Auth\Test\Builder\UserBuilder;
use App\Parser\Entity\Parser\ParserId;
use App\Parser\Entity\Task\Task;
use App\Parser\Entity\Task\TaskId;
use DateTimeImmutable;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;

final class RequestFixture extends AbstractFixture
{
    public const string TASK_ID = '910d0dbf-6292-4d2a-8c04-9e0c9618d7d4';
    public const string PARSER_ID = 'e6c0b4cf-a4b4-4c3f-9129-d17796cfc4b8';
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

        $task = new Task(
            taskId: new TaskId(self::TASK_ID),
            parserId: new ParserId(self::PARSER_ID),
            branchId: '000000',
            ticketId: '00000000-0000-0000-0000-000000000001',
            createdAt: new DateTimeImmutable(),
        );

        $task->ended('{test}');

        $manager->persist($task);

        $manager->flush();
    }
}
