<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateExampleNotesTable extends AbstractMigration
{
    public function change(): void
    {
        if ($this->hasTable('example_notes')) {
            return;
        }

        $this->table('example_notes')
            ->addColumn('title', 'string', ['limit' => 120])
            ->addColumn('body', 'text')
            ->addTimestamps()
            ->create();
    }
}
