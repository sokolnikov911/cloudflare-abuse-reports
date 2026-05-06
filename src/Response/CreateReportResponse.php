<?php

declare(strict_types=1);

namespace CloudflareAbuse\Response;

class CreateReportResponse
{
    public function __construct(
        public readonly string $abuseRand,
        public readonly ?array $request = null,
        public readonly ?string $result = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            abuseRand: $data['abuse_rand'] ?? '',
            request: $data['request'] ?? null,
            result: $data['result'] ?? null,
        );
    }
}
