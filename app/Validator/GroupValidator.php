<?php

declare(strict_types=1);

namespace App\Validator;

use App\DTO\GroupInput;

final class GroupValidator
{
    /** @return array<string, string> */
    public function validate(GroupInput $input): array
    {
        $errors = [];
        if ($input->code === '' || preg_match('/^[A-Z][A-Z0-9_]*$/', $input->code) !== 1) {
            $errors['code'] = 'Use letras maiusculas, numeros e sublinhado, iniciando por letra.';
        }
        if (trim($input->name) === '') {
            $errors['name'] = 'O nome e obrigatorio.';
        }
        return $errors;
    }
}
