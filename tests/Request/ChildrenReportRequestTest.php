<?php

declare(strict_types=1);

namespace CloudflareAbuse\Tests\Request;

use CloudflareAbuse\Enum\NotificationOption;
use CloudflareAbuse\Enum\ReportType;
use CloudflareAbuse\Request\ChildrenReportRequest;
use PHPUnit\Framework\TestCase;

class ChildrenReportRequestTest extends TestCase
{
    public function testGetType(): void
    {
        $request = new ChildrenReportRequest('a@b.com', 'Name', 'https://x.com');

        $this->assertSame(ReportType::Children, $request->getType());
    }

    public function testToArrayContainsRequiredFields(): void
    {
        $data = (new ChildrenReportRequest('a@b.com', 'Name', 'https://x.com'))->toArray();

        $this->assertSame('abuse_children', $data['act']);
        $this->assertSame('send', $data['host_notification']);
        $this->assertSame('send', $data['owner_notification']);
    }

    public function testToArrayOmitsOptionalFieldsWhenNull(): void
    {
        $data = (new ChildrenReportRequest('a@b.com', 'Name', 'https://x.com'))->toArray();

        $this->assertArrayNotHasKey('justification', $data);
        $this->assertArrayNotHasKey('country', $data);
        $this->assertArrayNotHasKey('ncmec_notification', $data);
    }

    public function testToArrayIncludesOptionalFields(): void
    {
        $request = new ChildrenReportRequest(
            email: 'a@b.com',
            name: 'Name',
            urls: 'https://x.com',
            justification: 'CSAM content',
            country: 'US',
            ncmecNotification: 'send',
        );

        $data = $request->toArray();

        $this->assertSame('CSAM content', $data['justification']);
        $this->assertSame('US', $data['country']);
        $this->assertSame('send', $data['ncmec_notification']);
    }

    public function testOwnerNotificationNone(): void
    {
        $request = new ChildrenReportRequest(
            email: 'a@b.com',
            name: 'Name',
            urls: 'https://x.com',
            ownerNotification: NotificationOption::None,
        );

        $this->assertSame('none', $request->toArray()['owner_notification']);
    }
}
