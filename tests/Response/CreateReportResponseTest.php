<?php

declare(strict_types=1);

namespace CloudflareAbuse\Tests\Response;

use CloudflareAbuse\Response\CreateReportResponse;
use PHPUnit\Framework\TestCase;

class CreateReportResponseTest extends TestCase
{
    public function testFromArrayMapsAbuseRand(): void
    {
        $response = CreateReportResponse::fromArray(['abuse_rand' => 'rand-abc-123']);

        $this->assertSame('rand-abc-123', $response->abuseRand);
    }

    public function testFromArrayMapsRequest(): void
    {
        $requestData = ['act' => 'abuse_phishing', 'email' => 'a@b.com'];
        $response    = CreateReportResponse::fromArray([
            'abuse_rand' => 'r1',
            'request'    => $requestData,
        ]);

        $this->assertSame($requestData, $response->request);
    }

    public function testFromArrayDefaultsToEmptyStringWhenAbuseRandMissing(): void
    {
        $response = CreateReportResponse::fromArray([]);

        $this->assertSame('', $response->abuseRand);
    }

    public function testFromArrayRequestIsNullWhenAbsent(): void
    {
        $response = CreateReportResponse::fromArray(['abuse_rand' => 'r1']);

        $this->assertNull($response->request);
    }
}
