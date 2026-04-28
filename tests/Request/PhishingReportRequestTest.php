<?php

declare(strict_types=1);

namespace CloudflareAbuse\Tests\Request;

use CloudflareAbuse\Enum\ReportType;
use CloudflareAbuse\Request\PhishingReportRequest;
use PHPUnit\Framework\TestCase;

class PhishingReportRequestTest extends TestCase
{
    public function testGetType(): void
    {
        $request = new PhishingReportRequest('a@b.com', 'Name', 'https://x.com');

        $this->assertSame(ReportType::Phishing, $request->getType());
    }

    public function testToArrayContainsRequiredFields(): void
    {
        $data = (new PhishingReportRequest('a@b.com', 'Name', 'https://x.com'))->toArray();

        $this->assertSame('abuse_phishing', $data['act']);
        $this->assertSame('a@b.com', $data['email']);
        $this->assertSame('a@b.com', $data['email2']);
        $this->assertSame('send', $data['host_notification']);
        $this->assertSame('send', $data['owner_notification']);
    }

    public function testToArrayOmitsOptionalFieldsWhenNull(): void
    {
        $data = (new PhishingReportRequest('a@b.com', 'Name', 'https://x.com'))->toArray();

        $this->assertArrayNotHasKey('justification', $data);
        $this->assertArrayNotHasKey('original_work', $data);
    }

    public function testToArrayIncludesOptionalFields(): void
    {
        $request = new PhishingReportRequest(
            email: 'a@b.com',
            name: 'Name',
            urls: 'https://x.com',
            justification: 'This is a phishing page',
            originalWork: 'https://legit.com',
        );

        $data = $request->toArray();

        $this->assertSame('This is a phishing page', $data['justification']);
        $this->assertSame('https://legit.com', $data['original_work']);
    }
}
