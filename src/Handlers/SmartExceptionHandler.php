<?php

namespace Codemonster\Errors\Handlers;

use Codemonster\Errors\Contracts\ExceptionHandlerInterface;
use Codemonster\Http\Response;
use Throwable;

class SmartExceptionHandler implements ExceptionHandlerInterface
{
    protected $viewRenderer;
    protected bool $debug;

    public function __construct(?callable $viewRenderer = null, bool $debug = false)
    {
        $this->viewRenderer = $viewRenderer;
        $this->debug = $debug;
    }

    public function handle(Throwable $e): Response
    {
        $status = 500;

        if (method_exists($e, 'getStatusCode')) {
            /** @var object{getStatusCode: callable(): int} $e */
            $status = $e->getStatusCode();
        }

        if ($this->debug) {
            return $this->renderTemplate('errors.debug', ['exception' => $e], $status) ?? $this->fallbackDebug($e);
        }

        return $this->renderTemplate(
            "errors.{$status}",
            [
                'status' => $status,
                'message' => $e->getMessage(),
                'exception' => $e,
            ],
            $status
        )
            ?? $this->renderTemplate(
                'errors.generic',
                [
                    'status' => $status,
                    'message' => $e->getMessage(),
                    'exception' => $e,
                ],
                $status
            )
            ?? $this->fallbackPlain($e, $status);
    }

    protected function renderTemplate(string $template, array $data, int $status): ?Response
    {
        if ($this->viewRenderer) {
            try {
                $html = call_user_func($this->viewRenderer, $template, $data);

                if ($html) {
                    return new Response($html, $status, ['Content-Type' => 'text/html']);
                }
            } catch (Throwable) {
            }
        }

        $basePath = dirname(__DIR__, 2) . '/resources/views/errors';
        $fileMap = [
            'errors.generic' => "$basePath/generic.php",
            'errors.debug'   => "$basePath/debug.php",
        ];

        if (isset($fileMap[$template]) && is_file($fileMap[$template])) {
            ob_start();
            extract($data, EXTR_SKIP);

            include $fileMap[$template];

            $html = ob_get_clean();

            return new Response($html, $status, ['Content-Type' => 'text/html']);
        }

        return null;
    }

    protected function fallbackPlain(Throwable $e, int $status): Response
    {
        $content = sprintf(
            "HTTP %d %s\nin %s:%d",
            $status,
            $e->getMessage(),
            $e->getFile(),
            $e->getLine()
        );

        return new Response($content, $status, ['Content-Type' => 'text/plain']);
    }

    protected function fallbackDebug(Throwable $e): Response
    {
        $content = sprintf(
            "[%s] %s\nin %s:%d\n\n%s",
            get_class($e),
            $e->getMessage(),
            $e->getFile(),
            $e->getLine(),
            $e->getTraceAsString()
        );

        return new Response($content, 500, ['Content-Type' => 'text/plain']);
    }
}
