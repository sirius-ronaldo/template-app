<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\HomeService;
use App\Support\Response;
use App\Support\View;

final class HomeController
{
    public function __construct(
        private readonly View $view,
        private readonly HomeService $homeService
    ) {
    }

    public function index(): Response
    {
        return Response::html($this->view->render('home/index', [
            'diagnostics' => $this->homeService->diagnostics(),
        ]));
    }
}
