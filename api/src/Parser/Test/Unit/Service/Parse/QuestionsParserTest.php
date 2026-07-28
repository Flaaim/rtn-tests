<?php

declare(strict_types=1);

namespace App\Parser\Test\Unit\Service\Parse;

use App\Parser\Entity\Parser\DTO\QuestionDTO;
use App\Parser\Exception\RemoteException;
use App\Parser\Service\Parse\QuestionsParser;
use App\Parser\Service\Sanitizer;
use DomainException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

/**
 * @internal
 * @coversNothing
 */
final class QuestionsParserTest extends TestCase
{
    public function testSuccess(): void
    {
        $host = 'http://example.com';
        $cookie = 'some_cookie';
        $branchId = 'branchId';
        $ticketId = 'ticketId';

        $mockClient = new MockHttpClient();

        $parser = new QuestionsParser($mockClient, new Sanitizer());
        $mockResponse = new MockResponse($this->getBody());
        $mockClient->setResponseFactory([$mockResponse]);

        $result = $parser->fetch($host, $cookie, $branchId, $ticketId);

        self::assertEquals([
            new QuestionDTO(
                '24d4d2ddec784e5298973804f294b056',
                1,
                'Что необходимо сделать в случае превышения установленной нормы заполнения тары хлором?',
                'http://example.com/QuestionImages/c91538/59a3f5a8-4a53-408e-bf9d-2844d8ab7977/10/1.jpg'
            ),
        ], $result);
    }

    public function testFailed(): void
    {
        $host = 'http://example.com';
        $cookie = 'some_cookie';
        $branchId = 'branchId';
        $ticketId = 'ticketId';

        $mockClient = new MockHttpClient();

        $parser = new QuestionsParser($mockClient, new Sanitizer());
        $mockResponse = new MockResponse('{}');
        $mockClient->setResponseFactory([$mockResponse]);

        self::expectException(RemoteException::class);
        self::expectExceptionMessage('Can not extract rows from response');
        $parser->fetch($host, $cookie, $branchId, $ticketId);
    }

    private function getBody(): string
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
            throw new DomainException('Can not encode response');
        }
        return $result;
    }
}
