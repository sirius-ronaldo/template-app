<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\ApplicationTypeInput;
use App\Repository\ApplicationTypeRepositoryInterface;
use App\Support\Pagination;
use App\Validator\CatalogValidator;

final class ApplicationTypeManagementService
{
    public function __construct(
        private readonly ApplicationTypeRepositoryInterface $types,
        private readonly CatalogValidator $validator
    ) {
    }

    /** @return array{items:list<array<string,mixed>>,total:int,pagination:Pagination} */
    public function list(string $filter, Pagination $pagination): array
    {
        $result = $this->types->paginate($filter, $pagination->offset(), $pagination->perPage);
        return $result + ['pagination' => new Pagination($pagination->page, $pagination->perPage, $result['total'])];
    }

    /** @return array<string,mixed>|null */
    public function find(int $id): ?array
    {
        return $this->types->find($id);
    }

    /** @return array<string,string> */
    public function create(ApplicationTypeInput $input): array
    {
        $input = $this->normalize($input);
        $errors = $this->validator->code($input->code, $input->name);
        if ($errors !== []) {
            return $errors;
        }
        if ($this->types->codeExists($input->code)) {
            return ['code' => 'O codigo informado ja existe.'];
        }
        $this->types->create($input->code, $input->name, $input->description, $input->active);
        return [];
    }

    /** @return array<string,string> */
    public function update(int $id, ApplicationTypeInput $input): array
    {
        if ($this->types->find($id) === null) {
            return ['global' => 'Tipo de aplicacao nao encontrado.'];
        }
        $input = $this->normalize($input);
        $errors = $this->validator->code($input->code, $input->name);
        if ($errors !== []) {
            return $errors;
        }
        if ($this->types->codeExists($input->code, $id)) {
            return ['code' => 'O codigo informado ja existe.'];
        }
        $this->types->update($id, $input->code, $input->name, $input->description, $input->active);
        return [];
    }

    /** @return array<string,string> */
    public function setActive(int $id, bool $active): array
    {
        $this->types->setActive($id, $active);
        return [];
    }

    /** @return array<string,string> */
    public function delete(int $id): array
    {
        try {
            $this->types->delete($id);
        } catch (\PDOException) {
            return ['global' => 'Nao foi possivel excluir o tipo por causa de dados relacionados.'];
        }
        return [];
    }

    /** @return list<array<string,mixed>> */
    public function listWithPermissions(): array
    {
        return $this->types->listWithPermissions();
    }

    private function normalize(ApplicationTypeInput $input): ApplicationTypeInput
    {
        return new ApplicationTypeInput(
            strtoupper(trim($input->code)),
            trim($input->name),
            trim($input->description),
            $input->active
        );
    }
}
