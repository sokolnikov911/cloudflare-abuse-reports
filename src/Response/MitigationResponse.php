<?php

declare(strict_types=1);

namespace CloudflareAbuse\Response;

class MitigationResponse
{
    public function __construct(
        public readonly string $id,
        public readonly string $status,
        public readonly string $type,
        public readonly ?string $effectiveDate = null,
        public readonly ?string $entityType = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? '',
            status: $data['status'] ?? '',
            type: $data['type'] ?? '',
            effectiveDate: $data['effective_date'] ?? null,
            entityType: $data['entity_type'] ?? null,
        );
    }
}
