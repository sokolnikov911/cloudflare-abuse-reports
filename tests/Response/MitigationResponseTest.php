<?php

declare(strict_types=1);

namespace CloudflareAbuse\Tests\Response;

use CloudflareAbuse\Response\MitigationResponse;
use PHPUnit\Framework\TestCase;

class MitigationResponseTest extends TestCase
{
    public function testFromArrayMapsAllFields(): void
    {
        $response = MitigationResponse::fromArray([
            'id'             => 'mit-1',
            'status'         => 'active',
            'type'           => 'legal_block',
            'effective_date' => '2024-07-01T00:00:00Z',
            'entity_type'    => 'url_pattern',
        ]);

        $this->assertSame('mit-1', $response->id);
        $this->assertSame('active', $response->status);
        $this->assertSame('legal_block', $response->type);
        $this->assertSame('2024-07-01T00:00:00Z', $response->effectiveDate);
        $this->assertSame('url_pattern', $response->entityType);
    }

    public function testFromArrayOptionalFieldsDefaultToNull(): void
    {
        $response = MitigationResponse::fromArray([
            'id'     => 'm1',
            'status' => 'pending',
            'type'   => 'account_suspend',
        ]);

        $this->assertNull($response->effectiveDate);
        $this->assertNull($response->entityType);
    }

    public function testFromArrayDefaultsEmptyStringsWhenKeysMissing(): void
    {
        $response = MitigationResponse::fromArray([]);

        $this->assertSame('', $response->id);
        $this->assertSame('', $response->status);
        $this->assertSame('', $response->type);
    }
}
