<?php

declare(strict_types=1);

namespace App\Support;

use App\Config\UiConfig;
use App\Service\AdminChecker;

final class UiContext
{
    public function __construct(
        private readonly UiConfig $config,
        private readonly SessionManager $sessions,
        private readonly AdminChecker $admins,
    ) {
    }

    /**
     * @return array{
     *     ui: UiConfig,
     *     auth: array{user_id:int,login:string,authenticated_at:int,last_activity_at:int}|null,
     *     isAdmin: bool,
     *     currentPath: string
     * }
     */
    public function data(): array
    {
        $auth = $this->sessions->credentials();
        $requestUri = (string) ($_SERVER['REQUEST_URI'] ?? '/');
        $path = parse_url($requestUri, PHP_URL_PATH);

        return [
            'ui' => $this->config,
            'auth' => $auth,
            'isAdmin' => $auth !== null && $this->admins->isAdmin($auth['user_id']),
            'currentPath' => is_string($path) && $path !== '' ? $path : '/',
        ];
    }
}
