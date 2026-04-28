<?php

declare(strict_types=1);

namespace CloudflareAbuse\Tests;

use CloudflareAbuse\Enum\MitigationStatus;
use CloudflareAbuse\Enum\ReportStatus;
use CloudflareAbuse\Enum\ReportType;
use CloudflareAbuse\ListReportsParams;
use DateTime;
use PHPUnit\Framework\TestCase;

class ListReportsParamsTest extends TestCase
{
    public function testToQueryParamsReturnsEmptyArrayWhenAllNull(): void
    {
        $this->assertSame([], (new ListReportsParams())->toQueryParams());
    }

    public function testToQueryParamsIncludesPagination(): void
    {
        $params = new ListReportsParams(page: 2, perPage: 50);
        $query  = $params->toQueryParams();

        $this->assertSame(2, $query['page']);
        $this->assertSame(50, $query['per_page']);
    }

    public function testToQueryParamsIncludesStatusEnum(): void
    {
        $params = new ListReportsParams(status: ReportStatus::InReview);
        $query  = $params->toQueryParams();

        $this->assertSame('in_review', $query['status']);
    }

    public function testToQueryParamsIncludesTypeEnum(): void
    {
        $params = new ListReportsParams(type: ReportType::Phishing);
        $query  = $params->toQueryParams();

        $this->assertSame('abuse_phishing', $query['type']);
    }

    public function testToQueryParamsIncludesMitigationStatusEnum(): void
    {
        $params = new ListReportsParams(mitigationStatus: MitigationStatus::Active);
        $query  = $params->toQueryParams();

        $this->assertSame('active', $query['mitigation_status']);
    }

    public function testToQueryParamsIncludesDomain(): void
    {
        $params = new ListReportsParams(domain: 'evil.com');

        $this->assertSame('evil.com', $params->toQueryParams()['domain']);
    }

    public function testToQueryParamsIncludesSort(): void
    {
        $params = new ListReportsParams(sort: 'created_at:desc');

        $this->assertSame('created_at:desc', $params->toQueryParams()['sort']);
    }

    public function testToQueryParamsFormatsCreatedAfterAsRfc3339(): void
    {
        $date   = new DateTime('2024-06-01T10:30:00+00:00');
        $params = new ListReportsParams(createdAfter: $date);
        $query  = $params->toQueryParams();

        $this->assertArrayHasKey('created_after', $query);
        $this->assertStringContainsString('2024-06-01', $query['created_after']);
    }

    public function testToQueryParamsFormatsCreatedBeforeAsRfc3339(): void
    {
        $date   = new DateTime('2024-12-31T23:59:59+00:00');
        $params = new ListReportsParams(createdBefore: $date);
        $query  = $params->toQueryParams();

        $this->assertArrayHasKey('created_before', $query);
        $this->assertStringContainsString('2024-12-31', $query['created_before']);
    }

    public function testToQueryParamsOmitsNullValues(): void
    {
        $params = new ListReportsParams(page: 1, domain: 'evil.com');
        $query  = $params->toQueryParams();

        $this->assertArrayHasKey('page', $query);
        $this->assertArrayHasKey('domain', $query);
        $this->assertArrayNotHasKey('per_page', $query);
        $this->assertArrayNotHasKey('status', $query);
        $this->assertArrayNotHasKey('type', $query);
        $this->assertArrayNotHasKey('sort', $query);
        $this->assertArrayNotHasKey('created_after', $query);
        $this->assertArrayNotHasKey('created_before', $query);
        $this->assertArrayNotHasKey('mitigation_status', $query);
    }
}
