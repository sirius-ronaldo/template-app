<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\PasswordChangeService;
use App\Support\Request;
use App\Support\Response;
use App\Support\SessionManager;
use App\Support\View;

final class PasswordController
{
    public function __construct(
        private readonly View $view,
        private readonly PasswordChangeService $passwords,
        private readonly SessionManager $sessions
    ) {
    }

    public function form(): Response
    {
        return Response::html($this->view->render('auth/password'));
    }

    public function change(Request $request): Response
    {
        $credentials = $this->sessions->credentials();
        if ($credentials === null) {
            return Response::redirect('/login');
        }

        $error = $this->passwords->change(
            $credentials['user_id'],
            (string) $request->input('current_password', ''),
            (string) $request->input('new_password', ''),
            (string) $request->input('new_password_confirmation', '')
        );
        if ($error !== null) {
            return Response::html($this->view->render('auth/password', ['error' => $error]), 422);
        }

        $this->sessions->logout();
        return Response::redirect('/login');
    }
}
