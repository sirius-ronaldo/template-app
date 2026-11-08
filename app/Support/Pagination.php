<?php

declare(strict_types=1);

namespace App\Support;

final class Pagination
{
    public const DEFAULT_PER_PAGE = 25;
    public const MAX_PER_PAGE = 100;

    public function __construct(
        public readonly int $page,
        public readonly int $perPage,
        public readonly int $total
    ) {
    }

    /** @param array<string, mixed> $query */
    public static function fromQuery(array $query, int $total): self
    {
        $page = max(1, (int) ($query['page'] ?? 1));
        $perPage = min(self::MAX_PER_PAGE, max(1, (int) ($query['per_page'] ?? self::DEFAULT_PER_PAGE)));
        return new self($page, $perPage, max(0, $total));
    }

    public function offset(): int
    {
        return ($this->page - 1) * $this->perPage;
    }

    public function pages(): int
    {
        return max(1, (int) ceil($this->total / $this->perPage));
    }

    /**
     * @param array<string, mixed> $query
     * @return array<string, string>
     */
    public function queryForPage(int $page, array $query = []): array
    {
        $result = [];
        foreach ($query as $key => $value) {
            if (is_scalar($value) && $key !== 'page') {
                $result[$key] = (string) $value;
            }
        }
        $result['page'] = (string) max(1, $page);
        $result['per_page'] = (string) $this->perPage;
        return $result;
    }
}
