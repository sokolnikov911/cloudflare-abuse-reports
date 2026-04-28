<?php

declare(strict_types=1);

namespace CloudflareAbuse\Tests\Request;

use CloudflareAbuse\Enum\NotificationOption;
use CloudflareAbuse\Enum\ReportType;
use CloudflareAbuse\Request\TrademarkReportRequest;
use PHPUnit\Framework\TestCase;

class TrademarkReportRequestTest extends TestCase
{
    private function make(array $overrides = []): TrademarkReportRequest
    {
        return new TrademarkReportRequest(
            email: 'test@example.com',
            name: 'John Doe',
            urls: 'https://example.com',
            trademarkNumber: $overrides['trademarkNumber'] ?? 'TM12345',
            trademarkOffice: $overrides['trademarkOffice'] ?? 'USPTO',
            trademarkSymbol: $overrides['trademarkSymbol'] ?? 'ACME',
            justification: $overrides['justification'] ?? null,
            hostNotification: $overrides['hostNotification'] ?? NotificationOption::Send,
            ownerNotification: $overrides['ownerNotification'] ?? NotificationOption::Send,
        );
    }

    public function testGetType(): void
    {
        $this->assertSame(ReportType::Trademark, $this->make()->getType());
    }

    public function testToArrayContainsRequiredFields(): void
    {
        $data = $this->make()->toArray();

        $this->assertSame('abuse_trademark', $data['act']);
        $this->assertSame('TM12345', $data['trademark_number']);
        $this->assertSame('USPTO', $data['trademark_office']);
        $this->assertSame('ACME', $data['trademark_symbol']);
        $this->assertSame('send', $data['host_notification']);
        $this->assertSame('send', $data['owner_notification']);
    }

    public function testToArrayOmitsJustificationWhenNull(): void
    {
        $this->assertArrayNotHasKey('justification', $this->make()->toArray());
    }

    public function testToArrayIncludesJustificationWhenProvided(): void
    {
        $data = $this->make(['justification' => 'Infringement details'])->toArray();

        $this->assertSame('Infringement details', $data['justification']);
    }

    public function testNotificationOptionSendAnon(): void
    {
        $data = $this->make([
            'hostNotification' => NotificationOption::SendAnon,
            'ownerNotification' => NotificationOption::SendAnon,
        ])->toArray();

        $this->assertSame('send-anon', $data['host_notification']);
        $this->assertSame('send-anon', $data['owner_notification']);
    }
}
