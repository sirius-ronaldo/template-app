<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Validator\CatalogValidator;
use PHPUnit\Framework\TestCase;

final class CatalogValidatorTest extends TestCase
{
    public function testAcceptsStandardCodeAndRejectsInvalidCodeOrEmptyName(): void
    {
        $validator = new CatalogValidator();

        self::assertSame([], $validator->code('READ_ONLY_2', 'Somente leitura'));
        self::assertArrayHasKey('code', $validator->code('2-READ', 'Leitura'));
        self::assertArrayHasKey('name', $validator->code('READ', ' '));
    }
}
