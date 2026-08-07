<?php

declare(strict_types=1);

namespace Tests\Functional\Admin\Testing\Course\Rename;

use App\Testing\Entity\Course\CourseId;
use App\Testing\Entity\Course\CourseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
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
    private readonly ContainerInterface $container;
    private readonly CourseRepository $courses;

    private readonly string $adminToken;
    private readonly string $userToken;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = self::createClient();

        $this->container = $this->client->getContainer();
        /** @var EntityManagerInterface $em */
        $em = $this->container->get(EntityManagerInterface::class);
        $this->courses = new CourseRepository($em);

        $fixturesLoader = new FixturesLoader($this->container);
        $fixturesLoader->loadFixtures([RequestFixture::class]);

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
        $this->client->jsonRequest('PUT', '/v1/admin/testing/courses/' . RequestFixture::COURSE_ID . '/rename');

        self::assertEquals(401, $this->client->getResponse()->getStatusCode());
    }

    public function testForbiddenForRegularUser(): void
    {
        $this->client->jsonRequest(
            'PUT',
            '/v1/admin/testing/courses/' . RequestFixture::COURSE_ID . '/rename',
            [
                'name' => 'New Name',
                'cipher' => 'aes-256',
            ],
            $this->authHeaders($this->userToken)
        );

        self::assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    public function testSuccess(): void
    {
        $this->client->jsonRequest(
            'PUT',
            '/v1/admin/testing/courses/' . RequestFixture::COURSE_ID . '/rename',
            [
                'name' => 'New Name',
                'cipher' => 'aes-256',
            ],
            $this->authHeaders($this->adminToken)
        );

        self::assertEquals(204, $this->client->getResponse()->getStatusCode());

        $course = $this->courses->get(new CourseId(RequestFixture::COURSE_ID));

        self::assertEquals('New Name', $course->getName());
        self::assertEquals('aes-256', $course->getCipher());
    }

    public function testEmpty(): void
    {
        $this->client->jsonRequest(
            'PUT',
            '/v1/admin/testing/courses/' . RequestFixture::COURSE_ID . '/rename',
            [],
            $this->authHeaders($this->adminToken)
        );

        self::assertEquals(422, $this->client->getResponse()->getStatusCode());

        self::assertJson($body = $this->client->getResponse()->getContent());

        $data = Json::decode($body);

        self::assertEquals(['errors' => [
            'name' => 'This value should not be blank.',
            'cipher' => 'This value should not be blank.',
        ]], $data);
    }

    public function testNotFound(): void
    {
        $this->client->jsonRequest(
            'PUT',
            '/v1/admin/testing/courses/' . RequestFixture::COURSE_NOT_FOUND_ID . '/rename',
            [
                'name' => 'New Name',
                'cipher' => 'aes-256',
            ],
            $this->authHeaders($this->adminToken)
        );

        self::assertEquals(409, $this->client->getResponse()->getStatusCode());

        self::assertJson($body = $this->client->getResponse()->getContent());
        $data = Json::decode($body);

        self::assertEquals([
            'message' => 'Course not found.',
        ], $data);
    }
}
