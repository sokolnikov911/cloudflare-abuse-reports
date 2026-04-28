<?php

declare(strict_types=1);

namespace CloudflareAbuse\Response;

class PaginatedResponse
{
    public function __construct(
        public readonly array $items,
        public readonly int $page,
        public readonly int $perPage,
        public readonly int $totalCount,
    ) {
    }

    public static function fromArray(array $data, callable $itemMapper): self
    {
        $info = $data['result_info'] ?? [];

        return new self(
            items: array_map($itemMapper, $data['result'] ?? []),
            page: $info['page'] ?? 1,
            perPage: $info['per_page'] ?? count($data['result'] ?? []),
            totalCount: $info['total_count'] ?? count($data['result'] ?? []),
        );
    }
}
