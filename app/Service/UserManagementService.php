<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\UserInput;
use App\Repository\UserRepositoryInterface;
use App\Support\Pagination;
use App\Support\PasswordHasher;
use App\Validator\UserValidator;
use PDOException;

final class UserManagementService
{
    public function __construct(
        private readonly UserRepositoryInterface $users,
        private readonly PasswordHasher $hasher,
        private readonly UserValidator $validator,
        private readonly AdminChecker $admins
    ) {
    }

    /** @return array{items:list<array<string,mixed>>,total:int,pagination:Pagination} */
    public function list(string $filter, Pagination $pagination): array
    {
        $result = $this->users->paginate($filter, $pagination->offset(), $pagination->perPage);
        return $result + ['pagination' => new Pagination($pagination->page, $pagination->perPage, $result['total'])];
    }

    /** @return array<string, mixed>|null */
    public function find(int $userId): ?array
    {
        return $this->users->find($userId);
    }

    /** @return array<string, string> */
    public function create(UserInput $input): array
    {
        $errors = $this->validator->validate($input, true);
        if ($errors !== []) {
            return $errors;
        }
        if ($this->users->loginExists($input->login)) {
            return ['login' => 'O login informado ja existe.'];
        }
        try {
            $this->users->create(
                $input->login,
                $input->name,
                $input->email,
                $input->active,
                $this->hasher->hash((string) $input->password)
            );
        } catch (PDOException) {
            return ['login' => 'O login informado ja existe.'];
        }
        return [];
    }

    /** @return array<string, string> */
    public function update(int $actorId, int $userId, UserInput $input): array
    {
        $errors = $this->validator->validate($input, false);
        if ($errors !== []) {
            return $errors;
        }
        if ($this->users->find($userId) === null) {
            return ['global' => 'Usuario nao encontrado.'];
        }
        if ($this->users->loginExists($input->login, $userId)) {
            return ['login' => 'O login informado ja existe.'];
        }
        if (!$input->active && $this->isSelfAdmin($actorId, $userId)) {
            return ['global' => 'O ADMIN nao pode inativar a propria conta.'];
        }
        $this->users->update($userId, $input->login, $input->name, $input->email, $input->active);
        return [];
    }

    /** @return array<string, string> */
    public function resetPassword(int $userId, string $password, string $confirmation): array
    {
        $errors = $this->validator->validatePassword($password, $confirmation);
        if ($errors !== []) {
            return $errors;
        }
        if ($this->users->find($userId) === null) {
            return ['global' => 'Usuario nao encontrado.'];
        }
        $this->users->updatePassword($userId, $this->hasher->hash($password));
        return [];
    }

    /** @return array<string, string> */
    public function deactivate(int $actorId, int $userId): array
    {
        if ($this->isSelfAdmin($actorId, $userId)) {
            return ['global' => 'O ADMIN nao pode inativar a propria conta.'];
        }
        $this->users->setActive($userId, false);
        return [];
    }

    /** @return array<string, string> */
    public function activate(int $userId): array
    {
        $this->users->setActive($userId, true);
        return [];
    }

    /** @return array<string, string> */
    public function delete(int $actorId, int $userId): array
    {
        if ($this->isSelfAdmin($actorId, $userId)) {
            return ['global' => 'O ADMIN nao pode excluir a propria conta.'];
        }
        try {
            $this->users->delete($userId);
        } catch (PDOException) {
            return ['global' => 'Nao foi possivel excluir o usuario por causa de dados relacionados.'];
        }
        return [];
    }

    private function isSelfAdmin(int $actorId, int $userId): bool
    {
        return $actorId === $userId && $this->admins->isAdmin($actorId);
    }
}
