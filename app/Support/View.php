<?php

declare(strict_types=1);

namespace App\Support;

use App\Config\UiConfig;
use App\Service\AdminChecker;

final class View
{
    public function __construct(
        private readonly string $basePath,
        private readonly Session $session,
        private readonly ?Csrf $defaultCsrf = null,
        private readonly ?AdminChecker $admins = null,
        private readonly ?UiContext $uiContext = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public function render(string $template, array $data = []): string
    {
        $viewPath = $this->basePath . '/' . $template . '.php';

        if (!is_file($viewPath)) {
            throw new \RuntimeException(sprintf('View "%s" nao encontrada.', $template));
        }

        $csrf = $data['csrf'] ?? $this->defaultCsrf;
        $context = $this->uiContext?->data() ?? [];
        $auth = $context['auth'] ?? $this->session->get('auth');
        $isAdmin = $context['isAdmin'] ?? (
            is_array($auth)
            && isset($auth['user_id'])
            && $this->admins?->isAdmin((int) $auth['user_id']) === true
        );
        $ui = $context['ui'] ?? UiConfig::defaults();
        $currentPath = $context['currentPath'] ?? '/';
        $flashSuccess = $this->session->consumeFlash('success');
        $flashError = $this->session->consumeFlash('error');

        extract($data, EXTR_SKIP);

        ob_start();
        require $viewPath;

        return (string) ob_get_clean();
    }

    public function escape(mixed $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }

    public function e(mixed $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}
