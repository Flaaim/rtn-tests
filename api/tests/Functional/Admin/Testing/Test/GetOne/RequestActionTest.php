<?php

declare(strict_types=1);

namespace Tests\Functional\Admin\Testing\Test\GetOne;

use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Tests\Functional\Admin\Course\Course\Get\RequestFixture as CourseGetRequestFixture;
use Tests\Functional\ArraySubsetAssertTrait;
use Tests\Functional\FixturesLoader;
use Tests\Functional\Json;
use Tests\Functional\OAuthTokenTrait;

/**
 * @internal
 * @coversNothing
 */
final class RequestActionTest extends WebTestCase
{
    use ArraySubsetAssertTrait;
    use OAuthTokenTrait;

    private readonly KernelBrowser $client;
    private readonly ContainerInterface $container;

    private string $adminToken;
    private string $userToken;

    protected function setUp(): void
    {
        $this->client = self::createClient();
        $this->container = $this->client->getContainer();

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
        $this->client->jsonRequest('GET', '/v1/admin/testing/tests/' . RequestFixture::TEST_ID);

        self::assertEquals(401, $this->client->getResponse()->getStatusCode());
    }

    public function testForbiddenForRegularUsers(): void
    {
        $this->client->jsonRequest(
            'GET',
            '/v1/admin/testing/tests/' . RequestFixture::TEST_ID,
            [],
            $this->authHeaders($this->userToken)
        );

        self::assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    public function testSuccess(): void
    {
        $this->client->jsonRequest(
            'GET',
            '/v1/admin/testing/tests/' . RequestFixture::TEST_ID,
            [],
            $this->authHeaders($this->adminToken)
        );

        self::assertEquals(200, $this->client->getResponse()->getStatusCode());

        self::assertJson($body = $this->client->getResponse()->getContent());

        $data = Json::decode($body);

        self::assertArraySubset([
            'id' => RequestFixture::TEST_ID,
            'name' => RequestFixture::TEST_NAME,
            'cipher' => RequestFixture::TEST_CIPHER,
            'description' => RequestFixture::TEST_NAME,
            'allowedMistakes' => 1,
            'courses' => [
                [
                    'id' => CourseGetRequestFixture::COURSE_ID,
                    'name' => CourseGetRequestFixture::COURSE_NAME,
                ],
            ],
            'tickets' => [
                [
                    'number' => 1,
                    'questions' => [
                        [
                            'id' => '90be077454a14f3d965c4b07645e3769',
                            'text' => 'Что необходимо сделать после восстановления самостоятельного дыхания у пострадавшего с отсутствующим сознанием?',
                            'questionImg' => '',
                            'answers' => [
                                [
                                    'id' => 'bbc14085f1e34ca93ccbbbd5ee9b5a01',
                                    'text' => 'Потормошить пострадавшего за плечи',
                                    'isCorrect' => false,
                                    'answerImg' => '',
                                ],
                                [
                                    'id' => '5a81b5f1089cee2b44809bfda245da59',
                                    'text' => 'Продолжить выполнять сердечно-легочную реанимацию до появления сознания у пострадавшего',
                                    'isCorrect' => false,
                                    'answerImg' => '',
                                ],
                                [
                                    'id' => 'a320df35029816f426dde35848e588bb',
                                    'text' => 'Дать пострадавшему понюхать нашатырный спирт',
                                    'isCorrect' => false,
                                    'answerImg' => '',
                                ],
                                [
                                    'id' => '93ff5fdd3e7eeb5cc38696beac126968',
                                    'text' => 'Придать пострадавшему устойчивое боковое положение',
                                    'isCorrect' => true,
                                    'answerImg' => '',
                                ],
                            ],
                            'form' => 'single_choice',
                        ],
                        [
                            'id' => '6724ac7652bc47d6913ab8ca11b2ea36',
                            'text' => 'На какое время допускается снять кровоостанавливающий жгут, если максимальное время его наложения истекло, а пострадавшего не транспортировали в медицинскую организацию?',
                            'questionImg' => '',
                            'answers' => [
                                [
                                    'id' => '310eb8b5ef4dc79b46e3f968819d0896',
                                    'text' => 'На 15 минут',
                                    'isCorrect' => false,
                                    'answerImg' => '',
                                ],
                                [
                                    'id' => '9f5608e80b8e5497fa0b42aaa3bbe7ae',
                                    'text' => 'На 10 минут',
                                    'isCorrect' => false,
                                    'answerImg' => '',
                                ],
                                [
                                    'id' => '4e98b49484d3755f1c80a4665db74091',
                                    'text' => 'На 30 минут',
                                    'isCorrect' => false,
                                    'answerImg' => '',
                                ],
                                [
                                    'id' => '66bc39ee7187f574dfb8699f74e55863',
                                    'text' => 'Снимать жгут не рекомендуется',
                                    'isCorrect' => true,
                                    'answerImg' => '',
                                ],
                            ],
                            'form' => 'single_choice',
                        ],
                    ],
                ],
            ],
            'slug' => '201',
            'createdAt' => new DateTimeImmutable($data['createdAt'])->format('Y-m-d'),
            'status' => $data['status'],
            'numberOfTickets' => $data['numberOfTickets'],
            'numberQuestionsInTicket' => $data['numberQuestionsInTicket'],
        ], $data);
    }

    public function testNotFound(): void
    {
        $this->client->jsonRequest(
            'GET',
            '/v1/admin/testing/tests/' . RequestFixture::TEST_NOT_FOUND_ID,
            [],
            $this->authHeaders($this->adminToken)
        );

        self::assertEquals(409, $this->client->getResponse()->getStatusCode());

        self::assertJson($body = $this->client->getResponse()->getContent());

        $data = Json::decode($body);

        self::assertEquals(['message' => 'Test not found.'], $data);
    }
}
