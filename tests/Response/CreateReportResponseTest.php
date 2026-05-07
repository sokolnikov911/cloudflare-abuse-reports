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

    public function testFromArrayMapsResult(): void
    {
        $response = CreateReportResponse::fromArray([
            'abuse_rand' => 'r1',
            'result'     => 'Successfully submitted, you should receive an email confirmation.',
        ]);

        $this->assertSame(
            'Successfully submitted, you should receive an email confirmation.',
            $response->result,
        );
    }

    public function testFromArrayResultIsNullWhenAbsent(): void
    {
        $response = CreateReportResponse::fromArray(['abuse_rand' => 'r1']);

        $this->assertNull($response->result);
    }

    public function testFromArrayMapsMsgAndErrCode(): void
    {
        $response = CreateReportResponse::fromArray([
            'result'   => 'error',
            'msg'      => 'You have already submitted this URL recently: https://example.com/foo',
            'err_code' => 'dedupe',
        ]);

        $this->assertSame(
            'You have already submitted this URL recently: https://example.com/foo',
            $response->msg,
        );
        $this->assertSame('dedupe', $response->err_code);
    }

    public function testFromArrayMsgAndErrCodeAreNullWhenAbsent(): void
    {
        $response = CreateReportResponse::fromArray(['abuse_rand' => 'r1']);

        $this->assertNull($response->msg);
        $this->assertNull($response->err_code);
    }
}
