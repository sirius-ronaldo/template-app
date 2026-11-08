<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\ApplicationInput;
use App\Repository\ApplicationRepositoryInterface;
use App\Repository\ApplicationTypeRepositoryInterface;
use App\Support\Pagination;
use App\Validator\CatalogValidator;

final class ApplicationManagementService
{
    public function __construct(
        private readonly ApplicationRepositoryInterface $applications,
        private readonly ApplicationTypeRepositoryInterface $types,
        private readonly CatalogValidator $validator
    ) {
    }

    /** @return array{items:list<array<string,mixed>>,total:int,pagination:Pagination} */
    public function list(string $filter, Pagination $pagination): array
    {
        $result = $this->applications->paginate($filter, $pagination->offset(), $pagination->perPage);
        return $result + ['pagination' => new Pagination($pagination->page, $pagination->perPage, $result['total'])];
    }

    /** @return array<string,mixed>|null */
    public function find(int $id): ?array
    {
        return $this->applications->find($id);
    }

    /** @return list<array<string,mixed>> */
    public function availableTypes(?int $currentId = null): array
    {
        $types = $this->types->listWithPermissions();
        if ($currentId === null) {
            return array_values(array_filter($types, static fn (array $type): bool => (bool) $type['active']));
        }
        $current = $this->applications->find($currentId);
        return array_values(array_filter(
            $types,
            static fn (array $type): bool => (bool) $type['active']
                || (int) $type['id'] === (int) ($current['type_id'] ?? 0)
        ));
    }

    /** @return array<string,string> */
    public function create(ApplicationInput $input): array
    {
        $input = $this->normalize($input);
        $errors = $this->validate($input);
        if ($errors !== []) {
            return $errors;
        }
        if ($this->applications->codeExists($input->code)) {
            return ['code' => 'O codigo informado ja existe.'];
        }
        if (!$this->typeIsActive($input->typeId)) {
            return ['type_id' => 'Selecione um tipo de aplicacao ativo.'];
        }
        $this->applications->create($input->typeId, $input->code, $input->name, $input->description, $input->active);
        return [];
    }

    /** @return array<string,string> */
    public function update(int $id, ApplicationInput $input): array
    {
        $current = $this->applications->find($id);
        if ($current === null) {
            return ['global' => 'Aplicacao nao encontrada.'];
        }
        $input = $this->normalize($input);
        $errors = $this->validate($input);
        if ($errors !== []) {
            return $errors;
        }
        if ($this->applications->codeExists($input->code, $id)) {
            return ['code' => 'O codigo informado ja existe.'];
        }
        if ($input->typeId !== (int) $current['type_id'] && !$this->typeIsActive($input->typeId)) {
            return ['type_id' => 'Selecione um tipo de aplicacao ativo.'];
        }
        $this->applications->update(
            $id,
            $input->typeId,
            $input->code,
            $input->name,
            $input->description,
            $input->active
        );
        return [];
    }

    /** @return array<string,string> */
    public function setActive(int $id, bool $active): array
    {
        if ($this->applications->find($id) === null) {
            return ['global' => 'Aplicacao nao encontrada.'];
        }
        $this->applications->setActive($id, $active);
        return [];
    }

    /** @return array<string,string> */
    public function delete(int $id): array
    {
        try {
            $this->applications->delete($id);
        } catch (\PDOException) {
            return ['global' => 'Nao foi possivel excluir a aplicacao por causa de dados relacionados. Inative-a.'];
        }
        return [];
    }

    /** @return array<string,string> */
    private function validate(ApplicationInput $input): array
    {
        $errors = $this->validator->code($input->code, $input->name);
        if ($input->typeId <= 0 || $this->types->find($input->typeId) === null) {
            $errors['type_id'] = 'O tipo de aplicacao e obrigatorio.';
        }
        return $errors;
    }

    private function typeIsActive(int $typeId): bool
    {
        $type = $this->types->find($typeId);
        return $type !== null && (bool) $type['active'];
    }

    private function normalize(ApplicationInput $input): ApplicationInput
    {
        return new ApplicationInput(
            strtoupper(trim($input->code)),
            trim($input->name),
            trim($input->description),
            $input->typeId,
            $input->active
        );
    }
}
