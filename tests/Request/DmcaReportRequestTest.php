<?php

declare(strict_types=1);

namespace CloudflareAbuse\Tests\Request;

use CloudflareAbuse\Enum\ReportType;
use CloudflareAbuse\Request\DmcaReportRequest;
use PHPUnit\Framework\TestCase;

class DmcaReportRequestTest extends TestCase
{
    private function make(array $overrides = []): DmcaReportRequest
    {
        return new DmcaReportRequest(
            email: $overrides['email'] ?? 'test@example.com',
            name: $overrides['name'] ?? 'John Doe',
            urls: $overrides['urls'] ?? 'https://example.com',
            originalWork: $overrides['originalWork'] ?? 'https://original.com',
            address1: $overrides['address1'] ?? '123 Main St',
            city: $overrides['city'] ?? 'New York',
            state: $overrides['state'] ?? 'NY',
            country: $overrides['country'] ?? 'US',
            agentName: $overrides['agentName'] ?? null,
        );
    }

    public function testGetType(): void
    {
        $this->assertSame(ReportType::Dmca, $this->make()->getType());
    }

    public function testToArrayContainsRequiredFields(): void
    {
        $data = $this->make()->toArray();

        $this->assertSame('abuse_dmca', $data['act']);
        $this->assertSame('test@example.com', $data['email']);
        $this->assertSame('test@example.com', $data['email2']);
        $this->assertSame('John Doe', $data['name']);
        $this->assertSame('https://example.com', $data['urls']);
        $this->assertSame('https://original.com', $data['original_work']);
        $this->assertSame('123 Main St', $data['address1']);
        $this->assertSame('New York', $data['city']);
        $this->assertSame('NY', $data['state']);
        $this->assertSame('US', $data['country']);
    }

    public function testToArraySetsSignatureToName(): void
    {
        $data = $this->make(['name' => 'Jane Smith'])->toArray();

        $this->assertSame('Jane Smith', $data['signature']);
    }

    public function testToArraySetsAgreeTo1(): void
    {
        $this->assertSame(1, $this->make()->toArray()['agree']);
    }

    public function testToArraySetsNotificationsToSend(): void
    {
        $data = $this->make()->toArray();

        $this->assertSame('send', $data['host_notification']);
        $this->assertSame('send', $data['owner_notification']);
    }

    public function testToArrayOmitsAgentNameWhenNull(): void
    {
        $this->assertArrayNotHasKey('agent_name', $this->make()->toArray());
    }

    public function testToArrayIncludesAgentNameWhenProvided(): void
    {
        $data = $this->make(['agentName' => 'My Agent'])->toArray();

        $this->assertSame('My Agent', $data['agent_name']);
    }

    public function testToArrayOmitsOptionalBaseFields(): void
    {
        $data = $this->make()->toArray();

        $this->assertArrayNotHasKey('company', $data);
        $this->assertArrayNotHasKey('comments', $data);
        $this->assertArrayNotHasKey('reported_country', $data);
        $this->assertArrayNotHasKey('reported_user_agent', $data);
        $this->assertArrayNotHasKey('tele', $data);
        $this->assertArrayNotHasKey('title', $data);
    }

    public function testToArrayIncludesOptionalBaseFieldsWhenProvided(): void
    {
        $request = new DmcaReportRequest(
            email: 'a@b.com',
            name: 'A',
            urls: 'https://x.com',
            originalWork: 'w',
            address1: 'a',
            city: 'c',
            state: 's',
            country: 'US',
            company: 'Acme',
            comments: 'A comment',
            tele: '123456',
        );

        $data = $request->toArray();

        $this->assertSame('Acme', $data['company']);
        $this->assertSame('A comment', $data['comments']);
        $this->assertSame('123456', $data['tele']);
    }
}
