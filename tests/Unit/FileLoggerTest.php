<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Support\FileLogger;
use PHPUnit\Framework\TestCase;

final class FileLoggerTest extends TestCase
{
    private string $path;

    protected function setUp(): void
    {
        parent::setUp();
        $this->path = sys_get_temp_dir() . '/template-app-test-' . bin2hex(random_bytes(8)) . '/app.log';
    }

    protected function tearDown(): void
    {
        @unlink($this->path);
        @rmdir(dirname($this->path));
        parent::tearDown();
    }

    public function testWritesStructuredRecordToConfiguredPath(): void
    {
        $logger = new FileLogger($this->path);

        $logger->info('Mensagem de teste', ['component' => 'test']);

        self::assertFileExists($this->path);
        $record = json_decode((string) file_get_contents($this->path), true, 512, JSON_THROW_ON_ERROR);
        self::assertSame('info', $record['level']);
        self::assertSame('Mensagem de teste', $record['message']);
        self::assertSame('test', $record['context']['component']);
    }
}
