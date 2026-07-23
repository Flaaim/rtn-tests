<?php

declare(strict_types=1);

namespace App\Parser\MessageHandler;

use App\Infrastructure\Doctrine\Flusher;
use App\Parser\Command\AuthRefresh\Command as AuthRefreshCommand;
use App\Parser\Command\AuthRefresh\Handler as AuthRefreshHandler;
use App\Parser\Entity\Task\TaskId;
use App\Parser\Entity\Task\TasksRepository;
use App\Parser\Event\ParseLaunched;
use App\Parser\Exception\RemoteException;
use App\Parser\Query\Parser\GetOne\Fetcher as ParserFetcher;
use App\Parser\Query\Parser\GetOne\Query as ParserGetQuery;
use App\Parser\Service\Parse\QuestionsParser;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class ParseLaunchedHandler
{
    public function __construct(
        private readonly QuestionsParser $questionsParser,
        private readonly AuthRefreshHandler $authRefreshHandler,
        private readonly ParserFetcher $parserFetcher,
        private readonly TasksRepository $tasks,
        private readonly Flusher $flusher,
    ) {}

    public function __invoke(ParseLaunched $command, bool $isRetry = false): void
    {
        $parser = $this->parserFetcher->fetch(new ParserGetQuery($command->parserId));
        $task = $this->tasks->get(new TaskId($command->taskId));

        try {
            $questions = $this->questionsParser->fetch(
                $parser->host,
                $parser->cookie,
                $command->branchId,
                $command->ticketId
            );
        } catch (RemoteException $e) {
            if ($isRetry) {
                $task->failed($e->getMessage());

                $this->flusher->flush();
                return;
            }
            $authRefreshCommand = new AuthRefreshCommand($command->parserId);
            $this->authRefreshHandler->handle($authRefreshCommand);

            $this($command, true);
            return;
        }

        $draft = json_encode($questions, JSON_THROW_ON_ERROR);

        $task->ended($draft);

        $this->flusher->flush();
    }
}
