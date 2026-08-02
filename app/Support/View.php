<?php

declare(strict_types=1);

namespace App\Support;

final class View
{
    public function __construct(
        private readonly string $basePath,
        private readonly Session $session,
        private readonly ?Csrf $defaultCsrf = null
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
        $auth = $this->session->get('auth');
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
}
