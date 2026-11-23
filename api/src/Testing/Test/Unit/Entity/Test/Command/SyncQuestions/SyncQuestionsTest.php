<?php

declare(strict_types=1);

namespace App\Testing\Test\Unit\Entity\Test\Command\SyncQuestions;

use App\Testing\Entity\Test\Settings;
use App\Testing\Event\TestChanged;
use App\Testing\Test\Builder\TestBuilder;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
final class SyncQuestionsTest extends TestCase
{
    public function testSyncQuestions(): void
    {
        $test = new TestBuilder()
            ->withQuestionIds([
                '0121b081-c461-42f0-b8ec-a4632a64faea',
                'b22c2959-2bb2-4e48-8d95-5ebd8de5b84d',
                '0121b081-c461-42f0-b8ec-a4632a64faea',
                '4d44d8d9-bcaf-4fea-9f03-7cf23e9e55df',
                '30606e75-9970-4ec9-9a78-b70bf5f828b6',
            ])->withSettings(
                new Settings(5, 2, 1)
            )
            ->active()
            ->build();

        $test->syncQuestions($this->getQuestionIds());
        self::assertEqualsCanonicalizing($this->getQuestionIds(), $test->getSequentialQuestions());

        $messages = $test->releaseEvents();

        $message = $messages[0];
        self::assertInstanceOf(TestChanged::class, $message);
        self::assertNotNull($message->id);
    }

    private function getQuestionIds(): array
    {
        return [
            'ad1388a2-ae3d-4d6d-bd39-0fc74c927acd',
            '7be866a0-038c-4eda-acaa-9b6754e50185',
            'bfd4c940-b935-47f4-824a-010801c7e9ab',
            'bce8ced4-a8ed-4228-9c1b-47fa319d46d9',
            'b382bb1e-628c-4c21-bfdb-e9e61309ff4d',
            '519091a8-4533-4ce1-b78f-4acac46199ad',
            'f1ef994e-0885-4097-b761-83907adb4c2e',
            '334d1323-24c2-4a9b-b78d-861bc7d6c67a',
            '489475db-a624-4091-ac84-2064cafbc2aa',
            '6bddfeae-df64-4b10-bba7-c9b5d445198a',
        ];
    }
}
