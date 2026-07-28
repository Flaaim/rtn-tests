<?php

declare(strict_types=1);

namespace App\Parser\Test\Unit\Service\Parse;

use App\Parser\Entity\Parser\DTO\AnswerDTO;
use App\Parser\Entity\Parser\DTO\QuestionDTO;
use App\Parser\Exception\RemoteException;
use App\Parser\Service\Parse\AnswersParser;
use App\Parser\Service\Sanitizer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

/**
 * @internal
 * @coversNothing
 */
final class AnswerParserTest extends TestCase
{
    public function testSuccess(): void
    {
        $cookie = 'some_cookie';
        $materialId = 'materialId';

        $mockClient = new MockHttpClient();

        $parser = new AnswersParser($mockClient, new Sanitizer());
        $mockResponse = new MockResponse($this->getBody());

        $mockClient->setResponseFactory([$mockResponse]);
        $questions = [new QuestionDTO('24d4d2ddec784e5298973804f294b056', 1, 'Что необходимо сделать в случае превышения установленной нормы заполнения тары хлором?', '')];

        $result = $parser->fetch($questions, $cookie, $materialId, $cookie);
        self::assertEquals([new QuestionDTO(
            '24d4d2ddec784e5298973804f294b056',
            1,
            'Что необходимо сделать в случае превышения установленной нормы заполнения тары хлором?',
            '',
            [
                new AnswerDTO(md5('Потормошить пострадавшего за плечи'), 'Потормошить пострадавшего за плечи', false, ''),
                new AnswerDTO(md5('Продолжить выполнять сердечно-легочную реанимацию до появления сознания у пострадавшего'), 'Продолжить выполнять сердечно-легочную реанимацию до появления сознания у пострадавшего', false, ''),
                new AnswerDTO(md5('Дать пострадавшему понюхать нашатырный спирт'), 'Дать пострадавшему понюхать нашатырный спирт', false, ''),
                new AnswerDTO(md5('Придать пострадавшему устойчивое боковое положение'), 'Придать пострадавшему устойчивое боковое положение', true, ''),
            ]
        ),
        ], $result);
    }

    public function testFailed(): void
    {
        $cookie = 'some_cookie';
        $materialId = 'materialId';

        $mockClient = new MockHttpClient();

        $parser = new AnswersParser($mockClient, new Sanitizer());
        $mockResponse = new MockResponse('{}');

        $mockClient->setResponseFactory([$mockResponse]);
        $questions = [new QuestionDTO('24d4d2ddec784e5298973804f294b056', 1, 'Что необходимо сделать в случае превышения установленной нормы заполнения тары хлором?', '')];

        self::expectException(RemoteException::class);
        self::expectExceptionMessage('Can not parse answers: {}');
        $parser->fetch($questions, $cookie, $materialId, $cookie);
    }

    private function getBody(): string
    {
        return '<h1><div><div>Что необходимо сделать после восстановления самостоятельного дыхания у пострадавшего с отсутствующим сознанием?</div></div></h1>
<div style="width: 30%;"></div>

<div class="block">
    <div class="table" data-bind="template: &#39;tableTemplate&#39;" id="tabled77097e0706f4f9fb70761db9a809fb7"></div><script type="text/javascript">$(function() { var table=$(\'#tabled77097e0706f4f9fb70761db9a809fb7\'), viewModel=new Table([{\'name\':\'Text\',\'caption\':\'Ответ\',\'enableSorting\':\'true\'},{\'name\':\'Correct\',\'caption\':\'Результат\',\'enableSorting\':\'true\',\'templateId\':\'answer-result\'}], {tableData:{"rowsCount":4,"rows":[{"Text":"<div><div>Потормошить пострадавшего за плечи</div></div>","Correct":false},{"Text":"<div><div>Продолжить выполнять сердечно-легочную реанимацию до появления сознания у пострадавшего</div></div>","Correct":false},{"Text":"<div><div>Дать пострадавшему понюхать нашатырный спирт</div></div>","Correct":false},{"Text":"<div><div>Придать пострадавшему устойчивое боковое положение</div></div>","Correct":true}]},sorting:false,paging:false,useCachedTableData:false,useSearchLoadSync:false,useSeed:false,alternativeView:false,sortingFields:[],menuShuffling:false,resetResults:false});table.data(\'viewModel\', viewModel);ko.applyBindings(viewModel, table[0]);})</script></div>';
    }
}
