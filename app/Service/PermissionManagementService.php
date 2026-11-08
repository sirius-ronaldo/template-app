<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\PermissionInput;
use App\Repository\PermissionRepositoryInterface;
use App\Support\Pagination;
use App\Validator\CatalogValidator;

final class PermissionManagementService
{
    public function __construct(
        private readonly PermissionRepositoryInterface $permissions,
        private readonly CatalogValidator $validator
    ) {
    }

    /** @return array{items:list<array<string,mixed>>,total:int,pagination:Pagination} */
    public function list(string $filter, Pagination $pagination): array
    {
        $result = $this->permissions->paginate($filter, $pagination->offset(), $pagination->perPage);
        return $result + ['pagination' => new Pagination($pagination->page, $pagination->perPage, $result['total'])];
    }

    /** @return array<string,mixed>|null */
    public function find(int $id): ?array
    {
        return $this->permissions->find($id);
    }

    /** @return array<string,string> */
    public function create(PermissionInput $input): array
    {
        $input = $this->normalize($input);
        $errors = $this->validator->code($input->code, $input->name);
        if ($errors !== []) {
            return $errors;
        }
        if ($this->permissions->codeExists($input->code)) {
            return ['code' => 'O codigo informado ja existe.'];
        }
        $this->permissions->create($input->code, $input->name, $input->description, $input->active);
        return [];
    }

    /** @return array<string,string> */
    public function update(int $id, PermissionInput $input): array
    {
        if ($this->permissions->find($id) === null) {
            return ['global' => 'Permissao nao encontrada.'];
        }
        $input = $this->normalize($input);
        $errors = $this->validator->code($input->code, $input->name);
        if ($errors !== []) {
            return $errors;
        }
        if ($this->permissions->codeExists($input->code, $id)) {
            return ['code' => 'O codigo informado ja existe.'];
        }
        $this->permissions->update($id, $input->code, $input->name, $input->description, $input->active);
        return [];
    }

    /** @return array<string,string> */
    public function setActive(int $id, bool $active): array
    {
        $this->permissions->setActive($id, $active);
        return [];
    }

    /** @return array<string,string> */
    public function delete(int $id): array
    {
        try {
            $this->permissions->delete($id);
        } catch (\PDOException) {
            return ['global' => 'Nao foi possivel excluir a permissao por causa de dados relacionados.'];
        }
        return [];
    }

    /** @return list<array<string,mixed>> */
    public function activeForType(int $typeId): array
    {
        return $this->permissions->activeForType($typeId);
    }

    /**
     * @param list<int> $permissionIds
     * @return array<string,string>
     */
    public function syncTypePermissions(int $typeId, array $permissionIds): array
    {
        try {
            $this->permissions->syncTypePermissions($typeId, $permissionIds);
        } catch (\Throwable $exception) {
            return ['global' => $exception->getMessage() === 'Permissao inexistente ou inativa.'
                ? $exception->getMessage()
                : 'Nao foi possivel atualizar as permissoes.'];
        }
        return [];
    }

    private function normalize(PermissionInput $input): PermissionInput
    {
        return new PermissionInput(
            strtoupper(trim($input->code)),
            trim($input->name),
            trim($input->description),
            $input->active
        );
    }
}
