<?php

declare(strict_types=1);

namespace App\Validator;

use App\DTO\UserInput;

final class UserValidator
{
    /** @return array<string, string> */
    public function validate(UserInput $input, bool $withPassword): array
    {
        $errors = [];
        if (trim($input->login) === '') {
            $errors['login'] = 'O login e obrigatorio.';
        }
        if (trim($input->name) === '') {
            $errors['name'] = 'O nome e obrigatorio.';
        }
        if (!filter_var($input->email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Informe um e-mail valido.';
        }
        if ($withPassword && strlen((string) $input->password) < 8) {
            $errors['password'] = 'A senha deve ter pelo menos 8 caracteres.';
        }
        if ($withPassword && $input->password !== $input->passwordConfirmation) {
            $errors['password_confirmation'] = 'A confirmacao da senha nao confere.';
        }
        return $errors;
    }

    /** @return array<string, string> */
    public function validatePassword(string $password, string $confirmation): array
    {
        $errors = [];
        if (strlen($password) < 8) {
            $errors['password'] = 'A senha deve ter pelo menos 8 caracteres.';
        }
        if ($password !== $confirmation) {
            $errors['password_confirmation'] = 'A confirmacao da senha nao confere.';
        }
        return $errors;
    }
}
