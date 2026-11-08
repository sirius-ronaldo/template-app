<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\GroupInput;
use App\Repository\GroupRepositoryInterface;
use App\Support\Pagination;
use App\Validator\GroupValidator;

final class GroupManagementService
{
    public function __construct(
        private readonly GroupRepositoryInterface $groups,
        private readonly GroupValidator $validator
    ) {
    }

    /** @return array{items:list<array<string,mixed>>,total:int,pagination:Pagination} */
    public function list(string $filter, Pagination $pagination): array
    {
        $result = $this->groups->paginate($filter, $pagination->offset(), $pagination->perPage);
        return $result + ['pagination' => new Pagination($pagination->page, $pagination->perPage, $result['total'])];
    }

    /** @return array<string,mixed>|null */
    public function find(int $groupId): ?array
    {
        return $this->groups->find($groupId);
    }

    /** @return array<string,string> */
    public function create(GroupInput $input): array
    {
        $input = $this->normalize($input);
        $errors = $this->validator->validate($input);
        if ($errors !== []) {
            return $errors;
        }
        if ($this->groups->codeExists($input->code)) {
            return ['code' => 'O codigo informado ja existe.'];
        }
        $this->groups->create($input->code, $input->name, $input->description, $input->active);
        return [];
    }

    /** @return array<string,string> */
    public function update(int $groupId, GroupInput $input): array
    {
        $existing = $this->groups->find($groupId);
        if ($existing === null) {
            return ['global' => 'Grupo nao encontrado.'];
        }
        $input = $this->normalize($input);
        if ($existing['code'] === 'ADMIN') {
            $input = new GroupInput('ADMIN', $input->name, $input->description, true);
        }
        $errors = $this->validator->validate($input);
        if ($errors !== []) {
            return $errors;
        }
        if ($this->groups->codeExists($input->code, $groupId)) {
            return ['code' => 'O codigo informado ja existe.'];
        }
        $this->groups->update($groupId, $input->code, $input->name, $input->description, $input->active);
        return [];
    }

    /** @return array<string,string> */
    public function deactivate(int $groupId): array
    {
        $group = $this->groups->find($groupId);
        if ($group === null) {
            return ['global' => 'Grupo nao encontrado.'];
        }
        if ($group['code'] === 'ADMIN') {
            return ['global' => 'O grupo ADMIN nao pode ser inativado.'];
        }
        $this->groups->setActive($groupId, false);
        return [];
    }

    /** @return array<string,string> */
    public function activate(int $groupId): array
    {
        $this->groups->setActive($groupId, true);
        return [];
    }

    /** @return array<string,string> */
    public function delete(int $groupId): array
    {
        $group = $this->groups->find($groupId);
        if ($group === null) {
            return ['global' => 'Grupo nao encontrado.'];
        }
        if ($group['code'] === 'ADMIN') {
            return ['global' => 'O grupo ADMIN nao pode ser excluido.'];
        }
        try {
            $this->groups->delete($groupId);
        } catch (\PDOException) {
            return ['global' => 'Nao foi possivel excluir o grupo por causa de dados relacionados.'];
        }
        return [];
    }

    /** @return list<array<string,mixed>> */
    public function userGroups(int $userId): array
    {
        return $this->groups->userGroups($userId);
    }

    /**
     * @param list<int> $groupIds
     * @return array<string,string>
     */
    public function syncUserGroups(int $userId, array $groupIds): array
    {
        try {
            $this->groups->syncUserGroups($userId, $groupIds);
        } catch (\Throwable $exception) {
            return ['global' => $exception->getMessage() === 'Grupo inexistente ou inativo.'
                ? $exception->getMessage()
                : 'Nao foi possivel atualizar os grupos.'];
        }
        return [];
    }

    private function normalize(GroupInput $input): GroupInput
    {
        return new GroupInput(
            strtoupper(trim($input->code)),
            trim($input->name),
            trim($input->description),
            $input->active
        );
    }
}
