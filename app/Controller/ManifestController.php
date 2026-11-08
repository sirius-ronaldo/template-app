<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\ManifestService;
use App\Support\Request;
use App\Support\Response;
use App\Support\SessionManager;
use App\Support\ViewRenderer;

final class ManifestController
{
    public function __construct(
        private readonly ViewRenderer $views,
        private readonly ManifestService $manifest,
        private readonly SessionManager $sessions
    ) {
    }

    public function show(Request $request): Response
    {
        return $this->render($request, $this->manifest->preview());
    }

    public function validate(Request $request): Response
    {
        return $this->render($request, $this->manifest->preview());
    }

    public function preview(Request $request): Response
    {
        return $this->render($request, $this->manifest->preview());
    }

    public function apply(Request $request): Response
    {
        $credentials = $this->sessions->credentials();
        $result = $this->manifest->apply($credentials['login'] ?? 'desconhecido');
        return $this->render($request, $result);
    }

    /** @param array<string,mixed> $result */
    private function render(Request $request, array $result): Response
    {
        return Response::html(
            $this->views->render(
                'admin/manifest/index',
                $result,
                $request->isHtmx() ? null : 'layouts/app'
            )
        );
    }
}
