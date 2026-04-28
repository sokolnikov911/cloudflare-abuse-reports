<?php

declare(strict_types=1);

namespace CloudflareAbuse\Response;

class ReportResponse
{
    public function __construct(
        public readonly string $id,
        public readonly string $domain,
        public readonly string $status,
        public readonly string $type,
        public readonly string $cdate,
        public readonly ?array $mitigationSummary = null,
        public readonly ?array $submitter = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? '',
            domain: $data['domain'] ?? '',
            status: $data['status'] ?? '',
            type: $data['type'] ?? '',
            cdate: $data['cdate'] ?? '',
            mitigationSummary: $data['mitigation_summary'] ?? null,
            submitter: $data['submitter'] ?? null,
        );
    }
}
