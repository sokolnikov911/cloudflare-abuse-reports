<?php

declare(strict_types=1);

namespace CloudflareAbuse\Tests\Request;

use CloudflareAbuse\Enum\NotificationOption;
use CloudflareAbuse\Enum\ReportType;
use CloudflareAbuse\Request\RegistrarWhoisReportRequest;
use PHPUnit\Framework\TestCase;

class RegistrarWhoisReportRequestTest extends TestCase
{
    public function testGetType(): void
    {
        $this->assertSame(
            ReportType::RegistrarWhois,
            (new RegistrarWhoisReportRequest('a@b.com', 'Name', 'https://x.com'))->getType(),
        );
    }

    public function testToArrayContainsRequiredFields(): void
    {
        $data = (new RegistrarWhoisReportRequest('a@b.com', 'Name', 'https://x.com'))->toArray();

        $this->assertSame('abuse_registrar_whois', $data['act']);
        $this->assertSame('send', $data['owner_notification']);
    }

    public function testToArrayOmitsRegWhoRequestWhenEmpty(): void
    {
        $this->assertArrayNotHasKey(
            'reg_who_request',
            (new RegistrarWhoisReportRequest('a@b.com', 'Name', 'https://x.com'))->toArray(),
        );
    }

    public function testToArrayIncludesRegWhoRequestWhenProvided(): void
    {
        $rdpData = ['field1' => 'value1', 'field2' => 'value2'];
        $request = new RegistrarWhoisReportRequest(
            email: 'a@b.com',
            name: 'Name',
            urls: 'https://x.com',
            regWhoRequest: $rdpData,
        );

        $this->assertSame($rdpData, $request->toArray()['reg_who_request']);
    }

    public function testOwnerNotificationNone(): void
    {
        $request = new RegistrarWhoisReportRequest(
            email: 'a@b.com',
            name: 'Name',
            urls: 'https://x.com',
            ownerNotification: NotificationOption::None,
        );

        $this->assertSame('none', $request->toArray()['owner_notification']);
    }
}
