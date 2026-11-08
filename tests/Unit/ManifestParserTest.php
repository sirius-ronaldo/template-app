<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Manifest\ManifestException;
use App\Manifest\ManifestParser;
use PHPUnit\Framework\TestCase;

final class ManifestParserTest extends TestCase
{
    /** @var list<string> */
    private array $files = [];

    protected function tearDown(): void
    {
        foreach ($this->files as $file) {
            @unlink($file);
        }
    }

    public function testParsesValidManifestAndDefaultsActive(): void
    {
        $yaml = <<<'YAML'
version: 1
permissoes:
  - codigo: READ
    nome: Leitura
tipos_aplicacoes:
  - codigo: WEB
    nome: Web
    permissoes: [READ]
aplicacoes:
  - codigo: PORTAL
    nome: Portal
    tipo: WEB
YAML;
        $parser = new ManifestParser($this->file($yaml));

        $manifest = $parser->parse();
        self::assertSame(1, $manifest->version);
        self::assertTrue($manifest->permissions[0]->active);
        self::assertSame(['READ'], $manifest->types[0]->permissions);
        self::assertTrue($manifest->applications[0]->active);
    }

    public function testRejectsVersionDuplicatesAndReferences(): void
    {
        $yaml = <<<'YAML'
version: 2
permissoes:
  - codigo: read
    nome: Leitura
  - codigo: READ
    nome: Outra
tipos_aplicacoes:
  - codigo: WEB
    nome: Web
    permissoes: [MISSING]
aplicacoes:
  - codigo: PORTAL
    nome: Portal
    tipo: UNKNOWN
YAML;
        $parser = new ManifestParser($this->file($yaml));

        $this->expectException(ManifestException::class);
        $this->expectExceptionMessage('A versao do manifesto deve ser 1.');
        $parser->parse();
    }

    public function testEmptyRequiredSectionsAndEmptyTypePermissionsAreValid(): void
    {
        $yaml = <<<'YAML'
version: 1
permissoes: []
tipos_aplicacoes:
  - codigo: WEB
    nome: Web
    permissoes: []
aplicacoes: []
YAML;
        $parser = new ManifestParser($this->file($yaml));
        $manifest = $parser->parse();
        self::assertCount(1, $manifest->types);
        self::assertSame([], $manifest->types[0]->permissions);
    }

    private function file(string $contents): string
    {
        $file = tempnam(sys_get_temp_dir(), 'manifest-');
        self::assertIsString($file);
        file_put_contents($file, $contents);
        $this->files[] = $file;
        return $file;
    }
}
