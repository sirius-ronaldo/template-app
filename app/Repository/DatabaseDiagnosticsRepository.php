<?php

declare(strict_types=1);

namespace App\Repository;

use PDO;

final class DatabaseDiagnosticsRepository
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    /**
     * @return array{database:string,user:string,server_version:string,now:string}
     */
    public function fetch(): array
    {
        $statement = $this->pdo->query(<<<'SQL'
            SELECT
                current_database() AS database,
                current_user AS "user",
                version() AS server_version,
                NOW()::text AS now
            SQL);

        /** @var array{database:string,user:string,server_version:string,now:string}|false $result */
        $result = $statement->fetch();

        if ($result === false) {
            return [
                'database' => '',
                'user' => '',
                'server_version' => '',
                'now' => '',
            ];
        }

        return $result;
    }
}
