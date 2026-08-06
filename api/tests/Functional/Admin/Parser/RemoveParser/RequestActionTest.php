<?php

declare(strict_types=1);

namespace Tests\Functional\Admin\Parser\RemoveParser;

use App\Parser\Entity\Parser\ParserId;
use App\Parser\Entity\Parser\ParserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
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
    private readonly KernelBrowser $client;
    private readonly ParserRepository $parsers;
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

    public function testUnauthenticatedReturn401(): void
    {
        $this->client->jsonRequest('DELETE', '/v1/admin/parsers/' . RequestFixture::PARSER_ID);

        self::assertEquals(401, $this->client->getResponse()->getStatusCode());
    }

    public function testForbiddenForRegularUser(): void
    {
        $this->client->jsonRequest(
            'DELETE',
            '/v1/admin/parsers/' . RequestFixture::PARSER_ID,
            [],
            $this->authHeaders($this->userToken)
        );

        self::assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    public function testSuccess(): void
    {
        $this->client->jsonRequest(
            'DELETE',
            '/v1/admin/parsers/' . RequestFixture::PARSER_ID,
            [],
            $this->authHeaders($this->adminToken)
        );

        self::assertEquals(204, $this->client->getResponse()->getStatusCode());

        $parser = $this->parsers->find(new ParserId(RequestFixture::PARSER_ID));
        self::assertNull($parser);
    }

    public function testNotFound(): void
    {
        $this->client->jsonRequest(
            'DELETE',
            '/v1/admin/parsers/' . RequestFixture::PARSER_NOT_FOUND,
            [],
            $this->authHeaders($this->adminToken)
        );

        self::assertEquals(409, $this->client->getResponse()->getStatusCode());

        self::assertJson($body = $this->client->getResponse()->getContent());

        $data = Json::decode($body);

        self::assertEquals([
            'message' => 'Parser not found.',
        ], $data);
    }

    public function testInvalid(): void
    {
        $this->client->jsonRequest('DELETE', '/v1/admin/parsers/invalid', [], $this->authHeaders($this->adminToken));

        self::assertEquals(422, $this->client->getResponse()->getStatusCode());

        self::assertJson($body = $this->client->getResponse()->getContent());

        $data = Json::decode($body);

        self::assertEquals(['errors' => [
            'id' => 'This is not a valid UUID.',
        ]], $data);
    }
}
