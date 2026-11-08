<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\AuthenticationService;
use App\Support\Request;
use App\Support\Response;
use App\Support\SessionManager;
use App\Support\View;

final class LoginController
{
    public function __construct(
        private readonly View $view,
        private readonly AuthenticationService $auth,
        private readonly SessionManager $sessions
    ) {
    }

    public function form(): Response
    {
        if ($this->sessions->credentials() !== null && !$this->sessions->isExpired()) {
            return Response::redirect('/Home');
        }
        return Response::html($this->view->render('auth/login'));
    }

    public function login(Request $request): Response
    {
        $login = (string) $request->input('login', '');
        $password = (string) $request->input('password', '');
        $user = $this->auth->authenticate($login, $password);
        if ($user === null) {
            return Response::html($this->view->render('auth/login', ['error' => 'Login ou senha invalidos.']), 422);
        }
        $this->sessions->authenticate($user);
        return Response::redirect('/Home');
    }
}
