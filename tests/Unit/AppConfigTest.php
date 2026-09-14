<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Config\AppConfig;
use DateTimeZone;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class AppConfigTest extends TestCase
{
    public function testCreatesConfigurationAndValidatesTimezone(): void
    {
        $config = AppConfig::fromEnvironment([
            'APP_ENV' => 'testing',
            'APP_DEBUG' => 'true',
            'APP_TIMEZONE' => 'UTC',
            'LOG_LEVEL' => 'debug',
            'DB_PORT' => '5433',
        ], '/app');

        self::assertSame('testing', $config->environment);
        self::assertTrue($config->debug);
        self::assertSame('UTC', $config->timezone);
        self::assertSame('debug', $config->logLevel);
        self::assertSame('/app/storage/logs/app.log', $config->logPath);
        self::assertSame(5433, $config->dbPort);
    }

    public function testRejectsUnknownTimezone(): void
    {
        $this->expectException(InvalidArgumentException::class);

        AppConfig::fromEnvironment(['APP_TIMEZONE' => 'Invalid/Timezone'], '/app');
    }

    public function testDefaultTimezoneIsAnInstalledTimezone(): void
    {
        $config = AppConfig::fromEnvironment([], '/app');

        self::assertContains($config->timezone, DateTimeZone::listIdentifiers());
    }
}
