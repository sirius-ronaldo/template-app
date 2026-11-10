<?php

declare(strict_types=1);

namespace App\Config;

use InvalidArgumentException;

final class UiConfig
{
    public function __construct(
        public readonly string $name,
        public readonly string $shortName,
        public readonly string $tagline,
        public readonly string $environmentLabel,
        public readonly string $defaultTheme,
        public readonly ?string $logoPath,
        public readonly ?string $loginHeaderPath,
        public readonly ?string $loginFooterPath,
    ) {
        if (!in_array($this->defaultTheme, ['dark', 'light'], true)) {
            throw new InvalidArgumentException('O tema padrao da interface deve ser dark ou light.');
        }
    }

    /** @param array<string, mixed> $values */
    public static function fromArray(array $values): self
    {
        return new self(
            name: self::requiredString($values, 'name'),
            shortName: self::requiredString($values, 'short_name'),
            tagline: self::requiredString($values, 'tagline'),
            environmentLabel: self::requiredString($values, 'environment_label'),
            defaultTheme: (string) ($values['default_theme'] ?? 'dark'),
            logoPath: self::nullableString($values['logo_path'] ?? null),
            loginHeaderPath: self::nullableString($values['login_header_path'] ?? null),
            loginFooterPath: self::nullableString($values['login_footer_path'] ?? null),
        );
    }

    public static function fromFile(string $path): self
    {
        if (!is_file($path)) {
            throw new InvalidArgumentException(sprintf('Configuracao de UI nao encontrada: %s.', $path));
        }

        $values = require $path;
        if (!is_array($values)) {
            throw new InvalidArgumentException('A configuracao de UI deve retornar um array.');
        }

        return self::fromArray($values);
    }

    public static function defaults(): self
    {
        return self::fromArray([
            'name' => 'Template App',
            'short_name' => 'TA',
            'tagline' => 'Base segura para novos projetos PHP',
            'environment_label' => 'Ambiente de desenvolvimento',
            'default_theme' => 'dark',
        ]);
    }

    /** @param array<string, mixed> $values */
    private static function requiredString(array $values, string $key): string
    {
        $value = trim((string) ($values[$key] ?? ''));
        if ($value === '') {
            throw new InvalidArgumentException(sprintf('Configuracao de UI obrigatoria: %s.', $key));
        }

        return $value;
    }

    private static function nullableString(mixed $value): ?string
    {
        $path = trim((string) ($value ?? ''));
        return $path === '' ? null : $path;
    }
}
