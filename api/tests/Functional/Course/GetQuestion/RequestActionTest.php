<?php

declare(strict_types=1);

namespace Tests\Functional\Course\GetQuestion;

use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Tests\Functional\FixturesLoader;
use Tests\Functional\Json;

/**
 * @internal
 * @coversNothing
 */
final class RequestActionTest extends WebTestCase
{
    private readonly KernelBrowser $client;
    private readonly ContainerInterface $container;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = self::createClient();

        $this->container = $this->client->getContainer();

        $fixturesLoader = new FixturesLoader($this->container);
        $fixturesLoader->loadFixtures([RequestFixture::class]);
    }

    public function testSuccess(): void
    {
        $this->client->jsonRequest(
            'GET',
            '/v1/questions/' . RequestFixture::QUESTION_ID,
        );

        self::assertEquals(200, $this->client->getResponse()->getStatusCode());

        self::assertJson($body = $this->client->getResponse()->getContent());

        $data = Json::decode($body);

        self::assertArrayHasKey('id', $data);
        self::assertArrayHasKey('text', $data);
        self::assertArrayHasKey('answers', $data);
        self::assertArrayHasKey('form', $data);
        self::assertArrayHasKey('test', $data);
        self::assertArrayHasKey('category', $data);

        $test = $data['test'];
        self::assertArrayHasKey('id', $test);
        self::assertArrayHasKey('name', $test);
        self::assertArrayHasKey('description', $test);
        self::assertArrayHasKey('slug', $test);

        $category = $data['category'];
        self::assertArrayHasKey('name', $category);
        self::assertArrayHasKey('slug', $category);
    }
}
