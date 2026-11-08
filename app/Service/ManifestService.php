<?php

declare(strict_types=1);

namespace App\Service;

use App\Manifest\ApplicationTypeDefinition;
use App\Manifest\ManifestDefinition;
use App\Manifest\ManifestDiff;
use App\Manifest\ManifestException;
use App\Manifest\ManifestParser;
use App\Manifest\ManifestSnapshot;
use App\Repository\ManifestRepository;
use Psr\Log\LoggerInterface;

final class ManifestService
{
    public function __construct(
        private readonly ManifestParser $parser,
        private readonly ManifestRepository $repository,
        private readonly LoggerInterface $logger
    ) {
    }

    /** @return array{valid:bool,diagnostics:list<string>,diff:ManifestDiff|null,manifest:ManifestDefinition|null} */
    public function preview(): array
    {
        try {
            $manifest = $this->parser->parse();
        } catch (ManifestException $exception) {
            return ['valid' => false, 'diagnostics' => $exception->diagnostics, 'diff' => null, 'manifest' => null];
        }
        return [
            'valid' => true,
            'diagnostics' => [],
            'diff' => $this->diff($manifest, $this->repository->snapshot()),
            'manifest' => $manifest,
        ];
    }

    /** @return array{valid:bool,diagnostics:list<string>,diff:ManifestDiff|null,manifest:ManifestDefinition|null,counts:array<string,int>|null} */
    public function apply(string $admin): array
    {
        $preview = $this->preview();
        if (!$preview['valid'] || $preview['manifest'] === null) {
            return $preview + ['counts' => null];
        }
        try {
            $counts = $this->repository->synchronize($preview['manifest']);
        } catch (\Throwable $exception) {
            $this->logger->error('Falha na sincronizacao do manifesto.', [
                'admin' => $admin,
                'version' => 1,
                'result' => 'rollback',
            ]);
            return [
                'valid' => false,
                'diagnostics' => ['Aplicacao cancelada: ' . $exception->getMessage()],
                'diff' => $preview['diff'],
                'manifest' => $preview['manifest'],
                'counts' => null,
            ];
        }
        $this->logger->info('Manifesto de seguranca sincronizado.', [
            'admin' => $admin,
            'version' => 1,
            'counts' => $counts,
            'result' => 'success',
        ]);
        return $preview + ['counts' => $counts];
    }

    private function diff(ManifestDefinition $manifest, ManifestSnapshot $snapshot): ManifestDiff
    {
        $declaredPermissions = array_fill_keys(
            array_map(static fn ($item): string => $item->code, $manifest->permissions),
            true
        );
        $declaredTypes = array_fill_keys(
            array_map(static fn ($item): string => $item->code, $manifest->types),
            true
        );
        $declaredApplications = array_fill_keys(
            array_map(static fn ($item): string => $item->code, $manifest->applications),
            true
        );
        $missing = [
            'permissoes' => array_values(array_diff(
                array_keys($snapshot->permissions),
                array_keys($declaredPermissions)
            )),
            'tipos_aplicacoes' => array_values(array_diff(
                array_keys($snapshot->types),
                array_keys($declaredTypes)
            )),
            'aplicacoes' => array_values(array_diff(
                array_keys($snapshot->applications),
                array_keys($declaredApplications)
            )),
        ];
        $relations = [];
        foreach ($manifest->types as $type) {
            $current = $snapshot->relations[$type->code] ?? [];
            $relations[] = count(array_diff($type->permissions, $current))
                + count(array_diff($current, $type->permissions));
        }
        return new ManifestDiff([
            'permissoes_inserir_atualizar' => count($manifest->permissions),
            'tipos_inserir_atualizar' => count($manifest->types),
            'aplicacoes_inserir_atualizar' => count($manifest->applications),
            'relacoes_alterar' => array_sum($relations),
        ], $missing);
    }
}
