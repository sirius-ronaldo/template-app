<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

final class ExampleNoteSeed extends AbstractSeed
{
    public function run(): void
    {
        $existing = $this->fetchRow("SELECT id FROM example_notes WHERE title = 'Primeira nota do template'");

        if ($existing !== false) {
            return;
        }

        $this->insert('example_notes', [[
            'title' => 'Primeira nota do template',
            'body' => 'Use esta tabela apenas como referencia inicial. Ajuste ou remova conforme o projeto real.',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]]);
    }
}
