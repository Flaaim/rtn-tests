<?php

declare(strict_types=1);

namespace Tests\Functional\Parser\AuthRefresh;

use App\Parser\Entity\Parser\ParserId;
use App\Parser\Entity\Parser\ParserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Tests\Functional\FixturesLoader;
use Tests\Functional\Json;

/**
 * @internal
 * @coversNothing
 */
final class RequestActionTest extends WebTestCase
{
    private readonly ParserRepository $parsers;
    private readonly KernelBrowser $client;
    private readonly ContainerInterface $container;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = self::createClient();

        $this->container = $this->client->getContainer();
        /** @var EntityManagerInterface $em */
        $em = $this->container->get(EntityManagerInterface::class);
        $this->parsers = new ParserRepository($em);

        $fixtureLoader = new FixturesLoader($this->container);
        $fixtureLoader->loadFixtures([RequestFixture::class]);
    }

    public function testSuccess(): void
    {
        $mockResponse = new MockResponse('{"url": "/Admin"}', [
            'http_code' => 204,
            'response_headers' => [
                'Set-Cookie: WorkplaceToken=e89a12fb-0227-49ff-884d-918fd9ae6f02; path=/; expires=Wed, 19 Jun 2526 23:24:17 GMT',
                'Set-Cookie: .OLIMPAUTH=; path=/Admin; expires=Mon, 11 Oct 1999 17:00:00 GMT',
                'Set-Cookie: .OLIMPAUTH=pDjbPRpZaz6AO913gO583vOCwin7zOTrWDnuSfQxUItgOslmytwr3UqxatGXBtR1mNl+At4Bq7amoCCrct3qxtO0uZtnU6K10hM+vHgKYBeYN55028I5N7MY07uVX3mT; path=/Admin',
                'Set-Cookie: .OLIMPROLES=; path=/Admin; expires=Mon, 11 Oct 1999 17:00:00 GMT',
            ],
        ]);

        $mockClient = $this->container->get(HttpClientInterface::class);
        $mockClient->setResponseFactory([$mockResponse]);

        $this->client->jsonRequest('PUT', '/v1/parser/refresh', ['parserId' => RequestFixture::PARSER_ID]);

        self::assertEquals(204, $this->client->getResponse()->getStatusCode());

        $parser = $this->parsers->find(new ParserId(RequestFixture::PARSER_ID));
        self::assertNotNull($parser);

        self::assertEquals(RequestFixture::PARSER_ID, $parser->getId()->getValue());
        self::assertEquals('.OLIMPAUTH=pDjbPRpZaz6AO913gO583vOCwin7zOTrWDnuSfQxUItgOslmytwr3UqxatGXBtR1mNl+At4Bq7amoCCrct3qxtO0uZtnU6K10hM+vHgKYBeYN55028I5N7MY07uVX3mT; path=/Admin .OLIMPROLES=; path=/Admin; expires=Mon, 11 Oct 1999 17:00:00 GMT WorkplaceToken=e89a12fb-0227-49ff-884d-918fd9ae6f02; path=/; expires=Wed, 19 Jun 2526 23:24:17 GMT', $parser->getCookie());
    }

    public function testNotFound(): void
    {
        $this->client->jsonRequest('PUT', '/v1/parser/refresh', ['parserId' => RequestFixture::PARSER_NOT_FOUND_ID]);

        self::assertEquals(409, $this->client->getResponse()->getStatusCode());
    }

    public function testInvalid(): void
    {
        $this->client->jsonRequest('PUT', '/v1/parser/refresh', ['parserId' => 'invalid string']);

        self::assertEquals(422, $this->client->getResponse()->getStatusCode());

        self::assertJson($body = $this->client->getResponse()->getContent());

        $data = Json::decode($body);

        self::assertEquals(['errors' => [
            'parserId' => 'This is not a valid UUID.',
        ]], $data);
    }

    public function testEmpty(): void
    {
        $this->client->jsonRequest('PUT', '/v1/parser/refresh', ['parserId' => '']);

        self::assertEquals(422, $this->client->getResponse()->getStatusCode());

        self::assertJson($body = $this->client->getResponse()->getContent());

        $data = Json::decode($body);

        self::assertEquals(['errors' => [
            'parserId' => 'This value should not be blank.',
        ]], $data);
    }
}
