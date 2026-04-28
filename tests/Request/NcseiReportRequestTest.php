<?php

declare(strict_types=1);

namespace CloudflareAbuse\Tests\Request;

use CloudflareAbuse\Enum\NotificationOption;
use CloudflareAbuse\Enum\ReportType;
use CloudflareAbuse\Request\NcseiReportRequest;
use PHPUnit\Framework\TestCase;

class NcseiReportRequestTest extends TestCase
{
    public function testGetType(): void
    {
        $this->assertSame(
            ReportType::Ncsei,
            (new NcseiReportRequest('a@b.com', 'Name', 'https://x.com'))->getType(),
        );
    }

    public function testToArrayContainsRequiredFields(): void
    {
        $data = (new NcseiReportRequest('a@b.com', 'Name', 'https://x.com'))->toArray();

        $this->assertSame('abuse_ncsei', $data['act']);
        $this->assertFalse($data['ncsei_subject_representation']);
        $this->assertSame('send', $data['host_notification']);
        $this->assertSame('send', $data['owner_notification']);
    }

    public function testNcseiSubjectRepresentationTrue(): void
    {
        $request = new NcseiReportRequest(
            email: 'a@b.com',
            name: 'Name',
            urls: 'https://x.com',
            ncseiSubjectRepresentation: true,
        );

        $this->assertTrue($request->toArray()['ncsei_subject_representation']);
    }

    public function testToArrayOmitsCountryWhenNull(): void
    {
        $this->assertArrayNotHasKey(
            'country',
            (new NcseiReportRequest('a@b.com', 'Name', 'https://x.com'))->toArray(),
        );
    }

    public function testToArrayIncludesCountryWhenProvided(): void
    {
        $request = new NcseiReportRequest(
            email: 'a@b.com',
            name: 'Name',
            urls: 'https://x.com',
            country: 'DE',
        );

        $this->assertSame('DE', $request->toArray()['country']);
    }

    public function testOwnerNotificationNone(): void
    {
        $request = new NcseiReportRequest(
            email: 'a@b.com',
            name: 'Name',
            urls: 'https://x.com',
            ownerNotification: NotificationOption::None,
        );

        $this->assertSame('none', $request->toArray()['owner_notification']);
    }
}
