<?php

declare(strict_types=1);

namespace App\Support;

final class UploadedFile
{
    public function __construct(
        private readonly string $clientFilename,
        private readonly string $clientMediaType,
        private readonly string $tmpPath,
        private readonly int $size,
        private readonly int $error
    ) {
    }

    /**
     * @param array{name:mixed,type:mixed,tmp_name:mixed,error:mixed,size:mixed} $file
     */
    public static function fromPhpArray(array $file): self
    {
        return new self(
            is_scalar($file['name']) ? (string) $file['name'] : '',
            is_scalar($file['type']) ? (string) $file['type'] : '',
            is_scalar($file['tmp_name']) ? (string) $file['tmp_name'] : '',
            (int) ($file['size'] ?? 0),
            (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE)
        );
    }

    public function clientFilename(): string
    {
        return $this->clientFilename;
    }

    public function clientMediaType(): string
    {
        return $this->clientMediaType;
    }

    public function tmpPath(): string
    {
        return $this->tmpPath;
    }

    public function size(): int
    {
        return $this->size;
    }

    public function error(): int
    {
        return $this->error;
    }

    public function hasFile(): bool
    {
        return $this->error !== UPLOAD_ERR_NO_FILE && $this->tmpPath !== '';
    }

    public function isValid(): bool
    {
        return $this->error === UPLOAD_ERR_OK && $this->tmpPath !== '' && is_file($this->tmpPath);
    }

    public function moveTo(string $targetPath): void
    {
        if (@move_uploaded_file($this->tmpPath, $targetPath)) {
            return;
        }

        if (@rename($this->tmpPath, $targetPath)) {
            return;
        }

        if (@copy($this->tmpPath, $targetPath)) {
            @unlink($this->tmpPath);

            return;
        }

        throw new \RuntimeException('Nao foi possivel mover o arquivo enviado.');
    }
}
