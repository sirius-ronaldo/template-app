<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Config\UiConfig;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class UiConfigTest extends TestCase
{
    public function testBuildsGenericBrandingConfiguration(): void
    {
        $config = UiConfig::fromArray([
            'name' => 'Minha Aplicação',
            'short_name' => 'MA',
            'tagline' => 'Descrição institucional',
            'environment_label' => 'Homologação',
            'default_theme' => 'light',
            'logo_path' => '/assets/img/logo.svg',
        ]);

        self::assertSame('Minha Aplicação', $config->name);
        self::assertSame('MA', $config->shortName);
        self::assertSame('light', $config->defaultTheme);
        self::assertSame('/assets/img/logo.svg', $config->logoPath);
        self::assertNull($config->loginHeaderPath);
    }

    public function testRejectsUnsupportedDefaultTheme(): void
    {
        $this->expectException(InvalidArgumentException::class);

        UiConfig::fromArray([
            'name' => 'Template App',
            'short_name' => 'TA',
            'tagline' => 'Template',
            'environment_label' => 'Teste',
            'default_theme' => 'automatic',
        ]);
    }
}
