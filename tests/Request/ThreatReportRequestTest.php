<?php

declare(strict_types=1);

namespace CloudflareAbuse\Tests\Request;

use CloudflareAbuse\Enum\ReportType;
use CloudflareAbuse\Request\ThreatReportRequest;
use PHPUnit\Framework\TestCase;

class ThreatReportRequestTest extends TestCase
{
    public function testGetType(): void
    {
        $this->assertSame(
            ReportType::Threat,
            (new ThreatReportRequest('a@b.com', 'Name', 'https://x.com'))->getType(),
        );
    }

    public function testToArrayContainsRequiredFields(): void
    {
        $data = (new ThreatReportRequest('a@b.com', 'Name', 'https://x.com'))->toArray();

        $this->assertSame('abuse_threat', $data['act']);
        $this->assertSame('send', $data['host_notification']);
        $this->assertSame('send', $data['owner_notification']);
    }

    public function testToArrayOmitsJustificationWhenNull(): void
    {
        $this->assertArrayNotHasKey(
            'justification',
            (new ThreatReportRequest('a@b.com', 'Name', 'https://x.com'))->toArray(),
        );
    }

    public function testToArrayIncludesJustification(): void
    {
        $request = new ThreatReportRequest(
            email: 'a@b.com',
            name: 'Name',
            urls: 'https://x.com',
            justification: 'Malware distribution site',
        );

        $this->assertSame('Malware distribution site', $request->toArray()['justification']);
    }
}
