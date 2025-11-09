<?php

namespace Codemonster\Errors\Tests;

use Codemonster\Errors\Handlers\SmartExceptionHandler;
use Codemonster\Http\Response;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class SmartExceptionHandlerTest extends TestCase
{
    protected function makeRenderer(): callable
    {
        return function (string $template, array $data) {
            return "<html><body>Template: {$template}, Message: {$data['exception']->getMessage()}</body></html>";
        };
    }

    public function testRendersDebugTemplateWhenDebugEnabled(): void
    {
        $handler = new SmartExceptionHandler($this->makeRenderer(), true);
        $response = $handler->handle(new RuntimeException('Debug error'));

        self::assertInstanceOf(Response::class, $response);
        self::assertStringContainsString('errors.debug', (string)$response);
        self::assertSame(500, $response->getStatusCode());
    }

    public function testRendersGenericTemplateWhenDebugDisabled(): void
    {
        $handler = new SmartExceptionHandler($this->makeRenderer(), false);
        $response = $handler->handle(new RuntimeException('Generic error'));

        self::assertStringContainsString('errors.500', (string)$response);
        self::assertSame(500, $response->getStatusCode());
    }

    public function testRenders404TemplateForNotFoundException(): void
    {
        $exception = new class('Page missing', 404) extends RuntimeException {
            public function getStatusCode(): int
            {
                return 404;
            }
        };

        $handler = new SmartExceptionHandler($this->makeRenderer(), false);
        $response = $handler->handle($exception);

        self::assertStringContainsString('errors.404', (string)$response);
        self::assertSame(404, $response->getStatusCode());
    }

    public function testFallbackDebugOutputWhenRendererFails(): void
    {
        $renderer = static function (): string {
            throw new RuntimeException('View failed');
        };

        $handler = new SmartExceptionHandler($renderer, true);
        $response = $handler->handle(new RuntimeException('Crash'));

        self::assertInstanceOf(Response::class, $response);
        self::assertStringContainsString('Crash', (string)$response);
        self::assertStringContainsString('RuntimeException', (string)$response);
    }
}
