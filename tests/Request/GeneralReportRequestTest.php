<?php

declare(strict_types=1);

namespace CloudflareAbuse\Tests\Request;

use CloudflareAbuse\Enum\NotificationOption;
use CloudflareAbuse\Enum\ReportType;
use CloudflareAbuse\Request\GeneralReportRequest;
use PHPUnit\Framework\TestCase;

class GeneralReportRequestTest extends TestCase
{
    public function testGetType(): void
    {
        $request = new GeneralReportRequest('a@b.com', 'Name', 'https://x.com');

        $this->assertSame(ReportType::General, $request->getType());
    }

    public function testToArrayContainsBaseAndNotificationFields(): void
    {
        $request = new GeneralReportRequest('a@b.com', 'Name', 'https://x.com');
        $data    = $request->toArray();

        $this->assertSame('abuse_general', $data['act']);
        $this->assertSame('send', $data['host_notification']);
        $this->assertSame('send', $data['owner_notification']);
    }

    public function testToArrayOmitsOptionalFieldsWhenNull(): void
    {
        $data = (new GeneralReportRequest('a@b.com', 'Name', 'https://x.com'))->toArray();

        $this->assertArrayNotHasKey('justification', $data);
        $this->assertArrayNotHasKey('destination_ips', $data);
        $this->assertArrayNotHasKey('ports_protocols', $data);
        $this->assertArrayNotHasKey('source_ips', $data);
    }

    public function testToArrayIncludesIpFields(): void
    {
        $request = new GeneralReportRequest(
            email: 'a@b.com',
            name: 'Name',
            urls: 'https://x.com',
            justification: 'reason',
            destinationIps: '10.0.0.1',
            portsProtocols: '443/TCP',
            sourceIps: '192.168.1.1',
        );

        $data = $request->toArray();

        $this->assertSame('reason', $data['justification']);
        $this->assertSame('10.0.0.1', $data['destination_ips']);
        $this->assertSame('443/TCP', $data['ports_protocols']);
        $this->assertSame('192.168.1.1', $data['source_ips']);
    }

    public function testSendAnonNotification(): void
    {
        $request = new GeneralReportRequest(
            email: 'a@b.com',
            name: 'Name',
            urls: 'https://x.com',
            hostNotification: NotificationOption::SendAnon,
            ownerNotification: NotificationOption::SendAnon,
        );

        $data = $request->toArray();

        $this->assertSame('send-anon', $data['host_notification']);
        $this->assertSame('send-anon', $data['owner_notification']);
    }
}
