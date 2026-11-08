<?php

declare(strict_types=1);

namespace App\Support;

final class ViewRenderer
{
    public function __construct(
        private readonly string $basePath,
        private readonly FlashBag $flash,
        private readonly ?CsrfTokenManager $csrf = null
    ) {
    }

    /** @param array<string, mixed> $data */
    public function render(string $template, array $data = [], ?string $layout = null): string
    {
        $content = $this->includeTemplate($template, $data);
        if ($layout === null) {
            return $content;
        }
        $layoutData = $data;
        $layoutData['content'] = $content;
        $layoutData['flashSuccess'] = $this->flash->pull('success');
        $layoutData['flashError'] = $this->flash->pull('error');
        return $this->includeTemplate($layout, $layoutData);
    }

    public function e(mixed $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    public function flash(string $key, mixed $default = null): mixed
    {
        return $this->flash->pull($key, $default);
    }

    public function csrfToken(): string
    {
        return $this->csrf?->token() ?? '';
    }

    /** @param array<string, mixed> $data */
    private function includeTemplate(string $template, array $data): string
    {
        $path = $this->basePath . '/' . trim($template, '/') . '.php';
        if (!is_file($path)) {
            throw new \RuntimeException(sprintf('View "%s" nao encontrada.', $template));
        }
        extract($data, EXTR_SKIP);
        ob_start();
        require $path;
        return (string) ob_get_clean();
    }
}
