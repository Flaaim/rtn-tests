<?php

declare(strict_types=1);

namespace Tests\Functional\Admin\Parser\LaunchParse\Handler;

use App\Parser\Entity\Task\TaskId;
use App\Parser\Entity\Task\TasksRepository;
use App\Parser\Event\ParseLaunched;
use App\Parser\MessageHandler\ParseLaunchedHandler;
use Doctrine\ORM\EntityManagerInterface;
use DomainException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Tests\Functional\Admin\Parser\LaunchParse\RequestFixture;
use Tests\Functional\FixturesLoader;

/**
 * @internal
 * @coversNothing
 */
final class ParseLauncherHandlerTest extends KernelTestCase
{
    private readonly ContainerInterface $container;
    private readonly TasksRepository $tasks;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->container = self::getContainer();

        $fixturesLoader = new FixturesLoader($this->container);
        $fixturesLoader->loadFixtures([RequestFixture::class]);

        /** @var EntityManagerInterface $em */
        $em = $this->container->get(EntityManagerInterface::class);
        $this->tasks = new TasksRepository($em);
    }

    public function testSuccess(): void
    {
        $mockQuestionsResponse = new MockResponse($this->getQuestionBody(), [
            'http_code' => 200,
            'response_headers' => [
                'Set-Cookie: WorkplaceToken=e89a12fb-0227-49ff-884d-918fd9ae6f02; path=/; expires=Wed, 19 Jun 2526 23:24:17 GMT',
                'Set-Cookie: .OLIMPAUTH=; path=/Admin; expires=Mon, 11 Oct 1999 17:00:00 GMT',
                'Set-Cookie: .OLIMPAUTH=pDjbPRpZaz6AO913gO583vOCwin7zOTrWDnuSfQxUItgOslmytwr3UqxatGXBtR1mNl+At4Bq7amoCCrct3qxtO0uZtnU6K10hM+vHgKYBeYN55028I5N7MY07uVX3mT; path=/Admin',
                'Set-Cookie: .OLIMPROLES=; path=/Admin; expires=Mon, 11 Oct 1999 17:00:00 GMT',
            ],
        ]);

        $mockAnswersResponse = new MockResponse($this->getAnswerBody(), [
            'http_code' => 200,
            'response_headers' => [
                'Set-Cookie: WorkplaceToken=e89a12fb-0227-49ff-884d-918fd9ae6f02; path=/; expires=Wed, 19 Jun 2526 23:24:17 GMT',
                'Set-Cookie: .OLIMPAUTH=; path=/Admin; expires=Mon, 11 Oct 1999 17:00:00 GMT',
                'Set-Cookie: .OLIMPAUTH=pDjbPRpZaz6AO913gO583vOCwin7zOTrWDnuSfQxUItgOslmytwr3UqxatGXBtR1mNl+At4Bq7amoCCrct3qxtO0uZtnU6K10hM+vHgKYBeYN55028I5N7MY07uVX3mT; path=/Admin',
                'Set-Cookie: .OLIMPROLES=; path=/Admin; expires=Mon, 11 Oct 1999 17:00:00 GMT',
            ],
        ]);
        $mockClient = $this->container->get(HttpClientInterface::class);
        $mockClient->setResponseFactory([$mockQuestionsResponse, $mockAnswersResponse]);

        $event = new ParseLaunched(
            RequestFixture::TASK_ID,
            RequestFixture::PARSER_ID,
            'branchId',
            'ticketId',
        );

        $handler = $this->container->get(ParseLaunchedHandler::class);
        $handler($event);

        $task = $this->tasks->get(new TaskId(RequestFixture::TASK_ID));
        self::assertNotNull($task);
        self::assertNull($task->getFailedReason());

        self::assertSame(2, $mockClient->getRequestsCount());
    }

    public function testSuccessWithRetry(): void
    {
        $mockResponse401 = new MockResponse('Auth error', ['http_code' => 401]);
        $responseAuthRefresh = new MockResponse('{"success": true}', [
            'http_code' => 200,
            'response_headers' => [
                'Set-Cookie: WorkplaceToken=e89a12fb-0227-49ff-884d-918fd9ae6f02; path=/; expires=Wed, 19 Jun 2526 23:24:17 GMT',
                'Set-Cookie: .OLIMPAUTH=; path=/Admin; expires=Mon, 11 Oct 1999 17:00:00 GMT',
                'Set-Cookie: .OLIMPAUTH=pDjbPRpZaz6AO913gO583vOCwin7zOTrWDnuSfQxUItgOslmytwr3UqxatGXBtR1mNl+At4Bq7amoCCrct3qxtO0uZtnU6K10hM+vHgKYBeYN55028I5N7MY07uVX3mT; path=/Admin',
                'Set-Cookie: .OLIMPROLES=; path=/Admin; expires=Mon, 11 Oct 1999 17:00:00 GMT',
            ],
        ]);

        $responseParserSuccess = new MockResponse('{"questions": []}', ['http_code' => 200]);

        $mockClient = $this->container->get(HttpClientInterface::class);
        $mockClient->setResponseFactory([$mockResponse401, $responseAuthRefresh, $responseParserSuccess]);

        $event = new ParseLaunched(
            RequestFixture::TASK_ID,
            RequestFixture::PARSER_ID,
            'branchId',
            'ticketId',
        );

        $handler = $this->container->get(ParseLaunchedHandler::class);

        $handler($event);

        $task = $this->tasks->get(new TaskId(RequestFixture::TASK_ID));
        self::assertNotNull($task);

        self::assertSame(3, $mockClient->getRequestsCount());
    }

    public function testRetryFailed(): void
    {
        $mockResponse = new MockResponse('Auth error', ['http_code' => 401]);

        $mockClient = $this->container->get(HttpClientInterface::class);
        $mockClient->setResponseFactory([$mockResponse]);

        $event = new ParseLaunched(
            RequestFixture::TASK_ID,
            RequestFixture::PARSER_ID,
            'branchId',
            'ticketId',
        );

        $handler = $this->container->get(ParseLaunchedHandler::class);

        $handler($event, true);

        $task = $this->tasks->get(new TaskId(RequestFixture::TASK_ID));

        self::assertNotNull($task->getFailedReason());
    }

    public function testParserNotFound(): void
    {
        $event = new ParseLaunched(
            RequestFixture::TASK_ID,
            RequestFixture::PARSER_NOT_FOUND_ID,
            'branchId',
            'ticketId',
        );

        $handler = $this->container->get(ParseLaunchedHandler::class);

        self::expectException(DomainException::class);
        self::expectExceptionMessage('Parser not found.');
        $handler($event);
    }

    public function testTaskNotFound(): void
    {
        $event = new ParseLaunched(
            RequestFixture::TASK_NOT_FOUND_ID,
            RequestFixture::PARSER_ID,
            'branchId',
            'ticketId',
        );

        $handler = $this->container->get(ParseLaunchedHandler::class);

        self::expectException(DomainException::class);
        self::expectExceptionMessage('Task not found.');
        $handler($event);
    }

    private function getQuestionBody(): string
    {
        $result = json_encode([
            'rowsCount' => 20,
            'rows' => [
                [
                    'Id' => '24d4d2ddec784e5298973804f294b056',
                    'Number' => 1,
                    'Text' => '<div><div>Что необходимо сделать в случае превышения установленной нормы заполнения тары хлором?</div></div>',
                    'QuestionMainImg' => '<div><div><img style="width: 300px;" src="/QuestionImages/c91538/59a3f5a8-4a53-408e-bf9d-2844d8ab7977/10/1.jpg" xmlns:xd="http://schemas.microsoft.com/office/infopath/2003" xd:content-type="png" /></div></div>',
                ],
            ],
        ]);
        if (false === $result) {
            throw new DomainException('Can not encode question body');
        }
        return $result;
    }

    private function getAnswerBody(): string
    {
        return '<h1><div><div>Что необходимо сделать после восстановления самостоятельного дыхания у пострадавшего с отсутствующим сознанием?</div></div></h1>
<div style="width: 30%;"></div>

<div class="block">
    <div class="table" data-bind="template: &#39;tableTemplate&#39;" id="tabled77097e0706f4f9fb70761db9a809fb7"></div><script type="text/javascript">$(function() { var table=$(\'#tabled77097e0706f4f9fb70761db9a809fb7\'), viewModel=new Table([{\'name\':\'Text\',\'caption\':\'Ответ\',\'enableSorting\':\'true\'},{\'name\':\'Correct\',\'caption\':\'Результат\',\'enableSorting\':\'true\',\'templateId\':\'answer-result\'}], {tableData:{"rowsCount":4,"rows":[{"Text":"<div><div>Потормошить пострадавшего за плечи</div></div>","Correct":false},{"Text":"<div><div>Продолжить выполнять сердечно-легочную реанимацию до появления сознания у пострадавшего</div></div>","Correct":false},{"Text":"<div><div>Дать пострадавшему понюхать нашатырный спирт</div></div>","Correct":false},{"Text":"<div><div>Придать пострадавшему устойчивое боковое положение</div></div>","Correct":true}]},sorting:false,paging:false,useCachedTableData:false,useSearchLoadSync:false,useSeed:false,alternativeView:false,sortingFields:[],menuShuffling:false,resetResults:false});table.data(\'viewModel\', viewModel);ko.applyBindings(viewModel, table[0]);})</script></div>';
    }
}
