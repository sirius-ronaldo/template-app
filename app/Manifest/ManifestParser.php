<?php

declare(strict_types=1);

namespace App\Manifest;

use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

final class ManifestParser
{
    public function __construct(private readonly string $path)
    {
    }

    public function parse(): ManifestDefinition
    {
        if (!is_file($this->path)) {
            throw new ManifestException(['Manifesto nao encontrado.']);
        }
        try {
            $data = Yaml::parseFile($this->path);
        } catch (ParseException $exception) {
            throw new ManifestException(['YAML invalido: ' . $exception->getMessage()]);
        }
        if (!is_array($data)) {
            throw new ManifestException(['A raiz do manifesto deve ser um mapa.']);
        }
        /** @var list<string> $diagnostics */
        $diagnostics = [];
        $version = $data['version'] ?? null;
        if (!is_int($version) || $version !== 1) {
            $diagnostics[] = 'A versao do manifesto deve ser 1.';
        }
        $permissions = $this->parsePermissions($data['permissoes'] ?? null, $diagnostics);
        $types = $this->parseTypes($data['tipos_aplicacoes'] ?? null, $diagnostics);
        $applications = $this->parseApplications($data['aplicacoes'] ?? null, $diagnostics);
        $permissionCodes = array_fill_keys(
            array_map(static fn (PermissionDefinition $item): string => $item->code, $permissions),
            true
        );
        $typeCodes = array_fill_keys(
            array_map(static fn (ApplicationTypeDefinition $item): string => $item->code, $types),
            true
        );
        foreach ($types as $type) {
            foreach ($type->permissions as $code) {
                if (!isset($permissionCodes[$code])) {
                    $diagnostics[] = sprintf('Tipo %s referencia permissao inexistente: %s.', $type->code, $code);
                }
            }
        }
        foreach ($applications as $application) {
            if (!isset($typeCodes[$application->type])) {
                $diagnostics[] = sprintf(
                    'Aplicacao %s referencia tipo inexistente: %s.',
                    $application->code,
                    $application->type
                );
            }
        }
        if ($diagnostics !== []) {
            throw new ManifestException($diagnostics);
        }
        return new ManifestDefinition($version, $permissions, $types, $applications);
    }

    /**
     * @param list<string> &$diagnostics
     * @param-out list<string> $diagnostics
     * @return list<PermissionDefinition>
     */
    private function parsePermissions(mixed $value, array &$diagnostics): array
    {
        if (!is_array($value)) {
            $diagnostics[] = 'A secao permissoes e obrigatoria e deve ser uma lista.';
            return [];
        }
        $items = [];
        $codes = [];
        foreach ($value as $index => $item) {
            if (!is_array($item)) {
                $diagnostics[] = "permissoes[$index] deve ser um mapa.";
                continue;
            }
            $definition = $this->catalogDefinition($item, "permissoes[$index]", $diagnostics);
            if ($definition === null) {
                continue;
            }
            if (isset($codes[$definition['code']])) {
                $diagnostics[] = "Codigo de permissao duplicado: {$definition['code']}.";
                continue;
            }
            $codes[$definition['code']] = true;
            $items[] = new PermissionDefinition(
                $definition['code'],
                $definition['name'],
                $definition['description'],
                $definition['active']
            );
        }
        return $items;
    }

    /**
     * @param list<string> &$diagnostics
     * @param-out list<string> $diagnostics
     * @return list<ApplicationTypeDefinition>
     */
    private function parseTypes(mixed $value, array &$diagnostics): array
    {
        if (!is_array($value)) {
            $diagnostics[] = 'A secao tipos_aplicacoes e obrigatoria e deve ser uma lista.';
            return [];
        }
        $items = [];
        $codes = [];
        foreach ($value as $index => $item) {
            if (!is_array($item)) {
                $diagnostics[] = "tipos_aplicacoes[$index] deve ser um mapa.";
                continue;
            }
            $definition = $this->catalogDefinition($item, "tipos_aplicacoes[$index]", $diagnostics);
            $permissions = $item['permissoes'] ?? null;
            if (
                !is_array($permissions)
                || array_filter($permissions, static fn (mixed $code): bool => !is_string($code)) !== []
            ) {
                $diagnostics[] = "tipos_aplicacoes[$index].permissoes e obrigatorio e deve ser uma lista de codigos.";
                continue;
            }
            if ($definition === null) {
                continue;
            }
            $permissionCodes = array_map(static fn (string $code): string => strtoupper(trim($code)), $permissions);
            if (count($permissionCodes) !== count(array_unique($permissionCodes))) {
                $diagnostics[] = "Permissoes duplicadas no tipo {$definition['code']}.";
            }
            if (isset($codes[$definition['code']])) {
                $diagnostics[] = "Codigo de tipo duplicado: {$definition['code']}.";
                continue;
            }
            $codes[$definition['code']] = true;
            $items[] = new ApplicationTypeDefinition(
                $definition['code'],
                $definition['name'],
                $definition['description'],
                $definition['active'],
                array_values(array_unique($permissionCodes))
            );
        }
        return $items;
    }

    /**
     * @param list<string> &$diagnostics
     * @param-out list<string> $diagnostics
     * @return list<ApplicationDefinition>
     */
    private function parseApplications(mixed $value, array &$diagnostics): array
    {
        if (!is_array($value)) {
            $diagnostics[] = 'A secao aplicacoes e obrigatoria e deve ser uma lista.';
            return [];
        }
        $items = [];
        $codes = [];
        foreach ($value as $index => $item) {
            if (!is_array($item)) {
                $diagnostics[] = "aplicacoes[$index] deve ser um mapa.";
                continue;
            }
            $definition = $this->catalogDefinition($item, "aplicacoes[$index]", $diagnostics);
            $type = $item['tipo'] ?? null;
            if (!is_string($type) || trim($type) === '') {
                $diagnostics[] = "aplicacoes[$index].tipo e obrigatorio.";
            }
            if ($definition === null || !is_string($type) || trim($type) === '') {
                continue;
            }
            if (isset($codes[$definition['code']])) {
                $diagnostics[] = "Codigo de aplicacao duplicado: {$definition['code']}.";
                continue;
            }
            $codes[$definition['code']] = true;
            $items[] = new ApplicationDefinition(
                $definition['code'],
                $definition['name'],
                $definition['description'],
                strtoupper(trim($type)),
                $definition['active']
            );
        }
        return $items;
    }

    /**
     * @param array<string,mixed> $item
     * @param list<string> &$diagnostics
     * @param-out list<string> $diagnostics
     * @return array{code:string,name:string,description:string,active:bool}|null
     */
    private function catalogDefinition(array $item, string $path, array &$diagnostics): ?array
    {
        $code = $item['codigo'] ?? null;
        $name = $item['nome'] ?? null;
        $description = $item['descricao'] ?? '';
        $active = $item['ativo'] ?? true;
        if (!is_string($code) || preg_match('/^[A-Z][A-Z0-9_]*$/', strtoupper(trim($code))) !== 1) {
            $diagnostics[] = "$path.codigo invalido.";
        }
        if (!is_string($name) || trim($name) === '') {
            $diagnostics[] = "$path.nome e obrigatorio.";
        }
        if (!is_string($description)) {
            $diagnostics[] = "$path.descricao deve ser texto.";
        }
        if (!is_bool($active)) {
            $diagnostics[] = "$path.ativo deve ser booleano.";
        }
        if (!is_string($code) || !is_string($name) || !is_string($description) || !is_bool($active)) {
            return null;
        }
        return [
            'code' => strtoupper(trim($code)),
            'name' => trim($name),
            'description' => trim($description),
            'active' => $active,
        ];
    }
}
