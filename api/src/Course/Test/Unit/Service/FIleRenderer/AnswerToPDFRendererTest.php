<?php

declare(strict_types=1);

namespace App\Course\Test\Unit\Service\FIleRenderer;

use App\Course\Service\FileRenderer\AnswersToPDFRenderer;
use Nucleos\DompdfBundle\Wrapper\DompdfWrapperInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Twig\Environment;
use Twig\Loader\ArrayLoader;

/**
 * @internal
 * @coversNothing
 */
final class AnswerToPDFRendererTest extends TestCase
{
    /** @var DompdfWrapperInterface&MockObject */
    private DompdfWrapperInterface $wrapper;

    protected function setUp(): void
    {
        parent::setUp();
        $this->wrapper = $this->createMock(DompdfWrapperInterface::class);
    }

    public function testRender(): void
    {
        $template = 'course/answers.html.twig';

        $loader = new ArrayLoader([
            $template => '<p>TEMPLATE</p>',
        ]);

        $twig = new Environment($loader);

        $renderer = new AnswersToPDFRenderer(
            $twig,
            $this->wrapper,
        );

        $this->wrapper->expects(self::once())
            ->method('getPdf')
            ->with(self::equalTo('<p>TEMPLATE</p>'))
            ->willReturn('<p>TEMPLATE</p>');

        $output = $renderer->render([], '1d71cd68-e737-4eb4-8395-5e8650fa3657');

        self::assertEquals('<p>TEMPLATE</p>', $output);
    }
}
