<?php

declare(strict_types=1);

namespace Tests\Functional\Parser\Create;

use App\Parser\Entity\Parser\Host;
use App\Parser\Entity\Parser\ParserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Tests\Functional\Json;

final class RequestActionTest extends WebTestCase
{
    private readonly ParserRepository $parsers;
    private readonly KernelBrowser $client;
    private readonly ContainerInterface $container;
    public function setUp(): void
    {
        parent::setUp();
        $this->client = self::createClient();

        $this->container = $this->client->getContainer();
        /** @var EntityManagerInterface $em */
        $em = $this->container->get(EntityManagerInterface::class);
        $this->parsers = new ParserRepository($em);
    }
    public function testEmpty(): void
    {
        $this->client->jsonRequest('POST', '/v1/parser');

        self::assertEquals(422, $this->client->getResponse()->getStatusCode());

        self::assertJson($body = $this->client->getResponse()->getContent());

        $data = Json::decode($body);

        self::assertEquals(['errors' => [
            'host' => 'This value should not be blank.',
            'login' => 'This value should not be blank.',
            'password' => 'This value should not be blank.',
        ]], $data);
    }

    public function testInvalidHost(): void
    {
        $this->client->jsonRequest('POST', '/v1/parser', [
            'host' => 'invalid',
            'login' => 'login',
            'password' => 'password',
        ]);

        self::assertEquals(422, $this->client->getResponse()->getStatusCode());

        self::assertJson($body = $this->client->getResponse()->getContent());

        $data = Json::decode($body);

        self::assertEquals(['errors' => [
            'host' => 'The url "invalid" is not a valid url'
        ]], $data);
    }

    public function testSuccess(): void
    {
        $this->client->catchExceptions(false);
        $mockResponse = new MockResponse('{"url": "/Admin"}', [
            'http_code' => 200,
            'response_headers' => [
                'Set-Cookie: WorkplaceToken=e89a12fb-0227-49ff-884d-918fd9ae6f02; path=/; expires=Wed, 19 Jun 2526 23:24:17 GMT',
                'Set-Cookie: .OLIMPAUTH=; path=/Admin; expires=Mon, 11 Oct 1999 17:00:00 GMT',
                'Set-Cookie: .OLIMPAUTH=pDjbPRpZaz6AO913gO583vOCwin7zOTrWDnuSfQxUItgOslmytwr3UqxatGXBtR1mNl+At4Bq7amoCCrct3qxtO0uZtnU6K10hM+vHgKYBeYN55028I5N7MY07uVX3mT; path=/Admin',
                'Set-Cookie: .OLIMPROLES=; path=/Admin; expires=Mon, 11 Oct 1999 17:00:00 GMT',
            ],
        ]);

        $mockClient = new MockHttpClient($mockResponse);
        $this->container->set(HttpClientInterface::class, $mockClient);

        $this->client->jsonRequest('POST', '/v1/parser', [
            'host' => 'http://example.com',
            'login' => 'login',
            'password' => 'password',
        ]);

        self::assertEquals(201, $this->client->getResponse()->getStatusCode());

        $parser = $this->parsers->findByHost(new Host('http://example.com'));
        self::assertNotNull($parser);
        self::assertEquals('http://example.com', $parser->getHost()->getValue());
        //$cookies[2] . ' ' . $cookies[3] . ' ' . $cookies[0];
        self::assertEquals('.OLIMPAUTH=pDjbPRpZaz6AO913gO583vOCwin7zOTrWDnuSfQxUItgOslmytwr3UqxatGXBtR1mNl+At4Bq7amoCCrct3qxtO0uZtnU6K10hM+vHgKYBeYN55028I5N7MY07uVX3mT; path=/Admin .OLIMPROLES=; path=/Admin; expires=Mon, 11 Oct 1999 17:00:00 GMT WorkplaceToken=e89a12fb-0227-49ff-884d-918fd9ae6f02; path=/; expires=Wed, 19 Jun 2526 23:24:17 GMT', $parser->getCookie());

    }
}
