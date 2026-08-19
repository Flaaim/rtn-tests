<?php

declare(strict_types=1);

namespace App\Testing\Test\Unit\Entity;

use App\Testing\Entity\Test\DTO\TicketDTO;
use App\Testing\Entity\Test\Settings;
use App\Testing\Entity\Test\Test;
use App\Testing\Entity\Test\TestId;
use App\Testing\Event\TestRemoved;
use App\Testing\Service\SlugGeneratorByCipher;
use App\Testing\Test\Builder\TestBuilder;
use DateTimeImmutable;
use DomainException;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
final class TestTest extends TestCase
{
    public function testTest(): void
    {
        $test = new Test(
            $id = TestId::generate(),
            $name = 'Test name',
            $cipher = 'ОТ 201.18',
            $description = 'Test Description',
            ['ba34f99c-6233-4be3-aa27-287d3726e54d'],
            $questionIds = ['5cf0b271-6e2b-4be9-b0b6-d0674947f0b4', '4f766a24-ea22-4237-924f-5c47b4e6ea5f'],
            $slug = 'ot201',
            $createdAt = new DateTimeImmutable(),
            new Settings(10, 10, 2)
        );

        self::assertEquals($id, $test->getId());
        self::assertEquals($name, $test->getName());
        self::assertEquals($cipher, $test->getCipher());
        self::assertEquals($description, $test->getDescription());
        self::assertEquals($questionIds, $test->getTickets()[0]->questionIds);
        self::assertFalse($test->isActive());
        self::assertEquals($slug, $test->getSlug());
        self::assertEquals($createdAt, $test->getCreatedAt());
        self::assertEquals(10, $test->getSettings()->getNumberOfTickets());
        self::assertEquals(10, $test->getSettings()->getNumberQuestionsInTicket());
        self::assertEquals(2, $test->getSettings()->getAllowedMistakes());
    }

    public function testGetSequenceQuestions(): void
    {
        $test = new TestBuilder()
            ->withQuestionIds(
                [
                    '0121b081-c461-42f0-b8ec-a4632a64faea',
                    '735eb05d-626b-4650-8146-ef1c7a77b5a9',
                    'b22c2959-2bb2-4e48-8d95-5ebd8de5b84d',
                    '0121b081-c461-42f0-b8ec-a4632a64faea',
                    '00c4fdb7-ce4f-41dd-935c-1e8d1475c25f',
                    '4d44d8d9-bcaf-4fea-9f03-7cf23e9e55df',
                ],
            )
            ->build();

        self::assertEquals([
            '0121b081-c461-42f0-b8ec-a4632a64faea',
            '735eb05d-626b-4650-8146-ef1c7a77b5a9',
            'b22c2959-2bb2-4e48-8d95-5ebd8de5b84d',
            '00c4fdb7-ce4f-41dd-935c-1e8d1475c25f',
            '4d44d8d9-bcaf-4fea-9f03-7cf23e9e55df',
        ], $test->getSequentialQuestions());
    }

    public function testActivate(): void
    {
        $test = new TestBuilder()
            ->build();

        $test->activate();

        self::assertTrue($test->isActive());
    }

    public function testDeactivate(): void
    {
        $test = new TestBuilder()
            ->active()
            ->build();

        $test->deactivate();

        self::assertFalse($test->isActive());
    }

    public function testRemove(): void
    {
        $test = new TestBuilder()
            ->build();

        $test->remove();

        $events = $test->releaseEvents();
        self::assertCount(1, $events);
        self::assertInstanceOf(TestRemoved::class, $events[0]);
    }

    public function testRemoveFailed(): void
    {
        $test = new TestBuilder()
            ->active()
            ->build();

        self::expectException(DomainException::class);
        self::expectExceptionMessage('Can not remove active test.');
        $test->remove();
    }

    public function testRename(): void
    {
        $test = new TestBuilder()
            ->withName('Name')
            ->withDescription('Description')
            ->build();

        $test->rename('Name1', 'Description1');

        self::assertEquals('Name1', $test->getName());
        self::assertEquals('Description1', $test->getDescription());
    }

    public function testRenameActive(): void
    {
        $test = new TestBuilder()
            ->withName('Name')
            ->withDescription('Description')
            ->active()
            ->build();

        self::expectException(DomainException::class);
        self::expectExceptionMessage('Can not rename active test.');
        $test->rename('Name2', 'Description');
    }

    public function testChangeCipher(): void
    {
        $test = new TestBuilder()
            ->withCipher('ОТ 201.18')
            ->build();
        $slugGenerator = new SlugGeneratorByCipher();
        $newCipher = 'ПБ 115.26';
        $newSlug = $slugGenerator->generate($newCipher);
        $test->changeCipher($newCipher, $newSlug);

        self::assertEquals($newCipher, $test->getCipher());
        self::assertEquals($newSlug, $test->getSlug());
    }

    public function testChangeCipherActive(): void
    {
        $test = new TestBuilder()
            ->withCipher('ОТ 201.18')
            ->active()
            ->build();
        $slugGenerator = new SlugGeneratorByCipher();
        $newCipher = 'ПБ 115.26';
        $newSlug = $slugGenerator->generate($newCipher);

        self::expectException(DomainException::class);
        self::expectExceptionMessage('Can not change cipher of active test.');
        $test->changeCipher($newCipher, $newSlug);
    }

    public function testUpdateTickets(): void
    {
        $test = new TestBuilder()
            ->withQuestionIds([
                '0121b081-c461-42f0-b8ec-a4632a64faea',
                'b22c2959-2bb2-4e48-8d95-5ebd8de5b84d',
                '0121b081-c461-42f0-b8ec-a4632a64faea',
                '4d44d8d9-bcaf-4fea-9f03-7cf23e9e55df',
            ])->build();

        $test->updateTickets(
            ['e06c3cd7-acad-4b9f-9617-67f327b323b8'],
            ['735eb05d-626b-4650-8146-ef1c7a77b5a9']
        );

        self::assertCount(1, $test->getTickets());
        self::assertFalse($test->isActive());
        self::assertEquals(['735eb05d-626b-4650-8146-ef1c7a77b5a9'], $test->getTickets()[0]->questionIds);
    }

    public function testUpdateTicketsActive(): void
    {
        $test = new TestBuilder()
            ->withQuestionIds(['735eb05d-626b-4650-8146-ef1c7a77b5a9'])
            ->active()
            ->build();

        self::expectException(DomainException::class);
        self::expectExceptionMessage('Can not update tickets of active test.');
        $test->updateTickets(['e06c3cd7-acad-4b9f-9617-67f327b323b8'], [new TicketDTO(1, ['735eb05d-626b-4650-8146-ef1c7a77b5a9'])]);
    }

    public function testChangeSettings(): void
    {
        $test = new TestBuilder()
            ->withSettings(new Settings(10, 10, 2))
            ->active()
            ->build();
    }
}
