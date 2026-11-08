<?php

declare(strict_types=1);

namespace App\Controller;

use App\Support\Response;
use App\Support\SessionManager;

final class LogoutController
{
    public function __construct(private readonly SessionManager $sessions)
    {
    }

    public function logout(): Response
    {
        $this->sessions->logout();
        return Response::redirect('/login');
    }
}
