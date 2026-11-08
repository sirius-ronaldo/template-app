<?php

declare(strict_types=1);

namespace App\Validator;

final class CatalogValidator
{
    /** @return array<string,string> */
    public function code(string $code, string $name): array
    {
        $errors = [];
        if (preg_match('/^[A-Z][A-Z0-9_]*$/', $code) !== 1) {
            $errors['code'] = 'Use letras maiusculas, numeros e sublinhado, iniciando por letra.';
        }
        if (trim($name) === '') {
            $errors['name'] = 'O nome e obrigatorio.';
        }
        return $errors;
    }
}
