<?php

declare(strict_types=1);

namespace CloudflareAbuse\Tests\Response;

use CloudflareAbuse\Response\ReportResponse;
use PHPUnit\Framework\TestCase;

class ReportResponseTest extends TestCase
{
    private function fixture(): array
    {
        return [
            'id'     => 'report-1',
            'domain' => 'evil.example.com',
            'status' => 'in_review',
            'type'   => 'abuse_phishing',
            'cdate'  => '2024-06-01T12:00:00Z',
        ];
    }

    public function testFromArrayMapsAllFields(): void
    {
        $response = ReportResponse::fromArray($this->fixture());

        $this->assertSame('report-1', $response->id);
        $this->assertSame('evil.example.com', $response->domain);
        $this->assertSame('in_review', $response->status);
        $this->assertSame('abuse_phishing', $response->type);
        $this->assertSame('2024-06-01T12:00:00Z', $response->cdate);
    }

    public function testFromArrayOptionalFieldsDefaultToNull(): void
    {
        $response = ReportResponse::fromArray($this->fixture());

        $this->assertNull($response->mitigationSummary);
        $this->assertNull($response->submitter);
    }

    public function testFromArrayMapsOptionalFields(): void
    {
        $summary   = ['count' => 1, 'status' => 'active'];
        $submitter = ['email' => 'a@b.com', 'name' => 'Alice'];

        $response = ReportResponse::fromArray(array_merge($this->fixture(), [
            'mitigation_summary' => $summary,
            'submitter'          => $submitter,
        ]));

        $this->assertSame($summary, $response->mitigationSummary);
        $this->assertSame($submitter, $response->submitter);
    }

    public function testFromArrayDefaultsEmptyStringsWhenKeysMissing(): void
    {
        $response = ReportResponse::fromArray([]);

        $this->assertSame('', $response->id);
        $this->assertSame('', $response->domain);
        $this->assertSame('', $response->status);
        $this->assertSame('', $response->type);
        $this->assertSame('', $response->cdate);
    }
}
