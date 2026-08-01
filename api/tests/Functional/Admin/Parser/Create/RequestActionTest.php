<?php

declare(strict_types=1);

namespace Tests\Functional\Admin\Parser\Create;

use App\Parser\Entity\Parser\Host;
use App\Parser\Entity\Parser\ParserRepository;
use App\Parser\Service\Encrypt\EncryptInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Tests\Functional\FixturesLoader;
use Tests\Functional\Json;
use Tests\Functional\OAuthTokenTrait;

/**
 * @internal
 * @coversNothing
 */
final class RequestActionTest extends WebTestCase
{
    use OAuthTokenTrait;

    private readonly ParserRepository $parsers;
    private readonly KernelBrowser $client;
    private readonly ContainerInterface $container;
    private string $adminToken;
    private string $userToken;

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

        $this->adminToken = $this->getAccessToken(
            $this->client,
            RequestFixture::ADMIN_EMAIL,
            RequestFixture::ADMIN_PASSWORD,
        );

        $this->userToken = $this->getAccessToken(
            $this->client,
            RequestFixture::USER_EMAIL,
            RequestFixture::USER_PASSWORD,
        );
    }

    public function testUnauthenticatedReturns401(): void
    {
        $this->client->jsonRequest('POST', '/v1/admin/parsers');

        self::assertEquals(401, $this->client->getResponse()->getStatusCode());
    }

    public function testForbiddenForRegularUser(): void
    {
        $this->client->jsonRequest(
            'POST',
            '/v1/admin/parsers',
            [],
            $this->authHeaders($this->userToken)
        );

        self::assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    public function testEmpty(): void
    {
        $this->client->jsonRequest(
            'POST',
            '/v1/admin/parsers',
            [],
            $this->authHeaders($this->adminToken)
        );

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
        $this->client->jsonRequest(
            'POST',
            '/v1/admin/parsers',
            [
                'host' => 'invalid',
                'login' => 'login',
                'password' => 'password',
            ],
            $this->authHeaders($this->adminToken)
        );

        self::assertEquals(422, $this->client->getResponse()->getStatusCode());

        self::assertJson($body = $this->client->getResponse()->getContent());

        $data = Json::decode($body);

        self::assertEquals(['errors' => [
            'host' => 'The url "invalid" is not a valid url',
        ]], $data);
    }

    public function testSuccess(): void
    {
        $this->client->disableReboot();
        $mockResponse = new MockResponse('{"url": "/Admin"}', info: [
            'http_code' => 200,
            'response_headers' => [
                'set-cookie' => [
                    'WorkplaceToken=e89a12fb-0227-49ff-884d-918fd9ae6f02; path=/; expires=Wed, 19 Jun 2526 23:24:17 GMT',
                    '.OLIMPAUTH=; path=/Admin; expires=Mon, 11 Oct 1999 17:00:00 GMT',
                    '.OLIMPAUTH=pDjbPRpZaz6AO913gO583vOCwin7zOTrWDnuSfQxUItgOslmytwr3UqxatGXBtR1mNl+At4Bq7amoCCrct3qxtO0uZtnU6K10hM+vHgKYBeYN55028I5N7MY07uVX3mT; path=/Admin',
                    '.OLIMPROLES=; path=/Admin; expires=Mon, 11 Oct 1999 17:00:00 GMT',
                ],
            ],
        ]);

        $mockClient = $this->client->getContainer()->get(HttpClientInterface::class);
        $mockClient->setResponseFactory([$mockResponse]);

        $this->client->jsonRequest(
            'POST',
            '/v1/admin/parsers',
            [
                'host' => 'http://example.com',
                'login' => 'login',
                'password' => 'password',
            ],
            $this->authHeaders($this->adminToken)
        );

        self::assertEquals(201, $this->client->getResponse()->getStatusCode());

        $parser = $this->parsers->findByHost(new Host('http://example.com'));
        self::assertNotNull($parser);
        self::assertEquals('http://example.com', $parser->getHost()->getValue());
        // $cookies[2] . ' ' . $cookies[3] . ' ' . $cookies[0];
        self::assertEquals('.OLIMPAUTH=pDjbPRpZaz6AO913gO583vOCwin7zOTrWDnuSfQxUItgOslmytwr3UqxatGXBtR1mNl+At4Bq7amoCCrct3qxtO0uZtnU6K10hM+vHgKYBeYN55028I5N7MY07uVX3mT; path=/Admin .OLIMPROLES=; path=/Admin; expires=Mon, 11 Oct 1999 17:00:00 GMT WorkplaceToken=e89a12fb-0227-49ff-884d-918fd9ae6f02; path=/; expires=Wed, 19 Jun 2526 23:24:17 GMT', $parser->getCookie());

        /** @var EncryptInterface $encryptService */
        $encryptService = $this->container->get(EncryptInterface::class);

        self::assertEquals('login', $encryptService->decrypt($parser->getCredentials()->getLogin()));
        self::assertEquals('password', $encryptService->decrypt($parser->getCredentials()->getPassword()));
    }
}
