<?php

declare(strict_types=1);

namespace Tests\Integration;

use App\Manifest\ApplicationDefinition;
use App\Manifest\ApplicationTypeDefinition;
use App\Manifest\ManifestDefinition;
use App\Manifest\PermissionDefinition;
use App\Repository\ManifestRepository;
use PHPUnit\Framework\TestCase;

final class ManifestRepositoryTest extends TestCase
{
    private ManifestRepository $repository;

    protected function setUp(): void
    {
        IntegrationDatabase::reset();
        $this->repository = new ManifestRepository(IntegrationDatabase::connection());
    }

    public function testSynchronizesCatalogAndRemovesUndeclaredTypeRelationsWithoutDeletingRecords(): void
    {
        $pdo = IntegrationDatabase::connection();
        $permission = new PermissionDefinition('READ', 'Leitura', '', true);
        $write = new PermissionDefinition('WRITE', 'Escrita', '', true);
        $type = new ApplicationTypeDefinition('WEB', 'Web', '', true, ['READ', 'WRITE']);
        $application = new ApplicationDefinition('PORTAL', 'Portal', '', 'WEB', true);
        $this->repository->synchronize(new ManifestDefinition(1, [$permission, $write], [$type], [$application]));

        self::assertSame(2, (int) $pdo->query('SELECT COUNT(*) FROM seg_permissoes')->fetchColumn());
        self::assertSame(2, (int) $pdo->query('SELECT COUNT(*) FROM seg_tipos_aplicacoes_permissoes')->fetchColumn());

        $typeWithoutWrite = new ApplicationTypeDefinition('WEB', 'Web atualizado', '', true, ['READ']);
        $this->repository->synchronize(
            new ManifestDefinition(1, [$permission, $write], [$typeWithoutWrite], [$application])
        );
        self::assertSame(
            1,
            (int) $pdo->query('SELECT COUNT(*) FROM seg_tipos_aplicacoes_permissoes')->fetchColumn()
        );
        self::assertSame(2, (int) $pdo->query('SELECT COUNT(*) FROM seg_permissoes')->fetchColumn());
    }

    public function testAbsentRecordsAreNotDeletedAndFailureRollsBack(): void
    {
        $pdo = IntegrationDatabase::connection();
        $pdo->exec("INSERT INTO seg_permissoes (seg_per_codigo, seg_per_nome) VALUES ('OLD', 'Antiga')");
        $permission = new PermissionDefinition('NEW', 'Nova', '', true);
        $type = new ApplicationTypeDefinition('WEB', 'Web', '', true, ['NEW']);
        $application = new ApplicationDefinition('PORTAL', 'Portal', '', 'WEB', true);
        $this->repository->synchronize(new ManifestDefinition(1, [$permission], [$type], [$application]));
        self::assertSame(2, (int) $pdo->query('SELECT COUNT(*) FROM seg_permissoes')->fetchColumn());

        $invalidType = new ApplicationTypeDefinition('WEB2', 'Web 2', '', true, ['MISSING']);
        $this->expectException(\Throwable::class);
        try {
            $this->repository->synchronize(new ManifestDefinition(1, [$permission], [$invalidType], []));
        } finally {
            self::assertSame(
                0,
                (int) $pdo->query(
                    "SELECT COUNT(*) FROM seg_tipos_aplicacoes WHERE seg_tap_codigo = 'WEB2'"
                )->fetchColumn()
            );
        }
    }
}
