<?php

declare(strict_types=1);

namespace CloudflareAbuse;

use CloudflareAbuse\Enum\MitigationStatus;
use CloudflareAbuse\Enum\ReportStatus;
use CloudflareAbuse\Enum\ReportType;
use DateTimeInterface;

class ListReportsParams
{
    public function __construct(
        public readonly ?int $page = null,
        public readonly ?int $perPage = null,
        public readonly ?string $sort = null,
        public readonly ?ReportStatus $status = null,
        public readonly ?ReportType $type = null,
        public readonly ?DateTimeInterface $createdAfter = null,
        public readonly ?DateTimeInterface $createdBefore = null,
        public readonly ?string $domain = null,
        public readonly ?MitigationStatus $mitigationStatus = null,
    ) {
    }

    public function toQueryParams(): array
    {
        $params = [];

        if ($this->page !== null) {
            $params['page'] = $this->page;
        }
        if ($this->perPage !== null) {
            $params['per_page'] = $this->perPage;
        }
        if ($this->sort !== null) {
            $params['sort'] = $this->sort;
        }
        if ($this->status !== null) {
            $params['status'] = $this->status->value;
        }
        if ($this->type !== null) {
            $params['type'] = $this->type->value;
        }
        if ($this->createdAfter !== null) {
            $params['created_after'] = $this->createdAfter->format(DateTimeInterface::RFC3339);
        }
        if ($this->createdBefore !== null) {
            $params['created_before'] = $this->createdBefore->format(DateTimeInterface::RFC3339);
        }
        if ($this->domain !== null) {
            $params['domain'] = $this->domain;
        }
        if ($this->mitigationStatus !== null) {
            $params['mitigation_status'] = $this->mitigationStatus->value;
        }

        return $params;
    }
}
