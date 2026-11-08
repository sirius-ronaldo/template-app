<?php

declare(strict_types=1);

namespace App\Http;

use App\Exception\AuthorizationException;
use App\Exception\NotFoundException;
use App\Support\Response;
use App\Support\View;
use Psr\Log\LoggerInterface;
use Throwable;

final class ExceptionHandler
{
    public function __construct(
        private readonly View $view,
        private readonly LoggerInterface $logger,
        private readonly bool $debug = false
    ) {
    }

    public function render(Throwable $exception): Response
    {
        $this->logger->error('Erro nao tratado na aplicacao.', ['exception' => $exception::class]);
        $status = $exception instanceof NotFoundException
            ? 404
            : ($exception instanceof AuthorizationException ? 403 : 500);
        $template = $status === 404 ? 'errors/404' : ($status === 403 ? 'errors/403' : 'errors/500');

        return Response::html($this->view->render($template, [
            'message' => $this->debug ? $exception->getMessage() : null,
        ]), $status);
    }
}
