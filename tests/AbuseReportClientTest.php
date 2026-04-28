<?php

declare(strict_types=1);

namespace CloudflareAbuse\Tests;

use CloudflareAbuse\AbuseReportClient;
use CloudflareAbuse\Exception\ApiException;
use CloudflareAbuse\HttpTransport\HttpResponse;
use CloudflareAbuse\HttpTransport\HttpTransportInterface;
use CloudflareAbuse\ListReportsParams;
use CloudflareAbuse\Request\PhishingReportRequest;
use CloudflareAbuse\Response\CreateReportResponse;
use CloudflareAbuse\Response\MitigationResponse;
use CloudflareAbuse\Response\PaginatedResponse;
use CloudflareAbuse\Response\ReportResponse;
use PHPUnit\Framework\TestCase;

class AbuseReportClientTest extends TestCase
{
    private const ACCOUNT = 'acc-123';
    private const TOKEN   = 'test-token';

    private function transport(int $status, mixed $body): HttpTransportInterface
    {
        $mock = $this->createMock(HttpTransportInterface::class);
        $mock->method('request')->willReturn(new HttpResponse($status, json_encode($body, JSON_THROW_ON_ERROR)));
        return $mock;
    }

    private function phishingRequest(): PhishingReportRequest
    {
        return new PhishingReportRequest(
            email: 'reporter@example.com',
            name: 'Jane Reporter',
            urls: 'https://evil.example.com',
            justification: 'Confirmed phishing page',
        );
    }

    // --- submitReport ---

    public function testSubmitReportCallsPostWithCorrectUrl(): void
    {
        $mock = $this->createMock(HttpTransportInterface::class);
        $mock->expects($this->once())
            ->method('request')
            ->with(
                'POST',
                'https://api.cloudflare.com/client/v4/accounts/' . self::ACCOUNT . '/abuse-reports/abuse_phishing',
                $this->callback(fn($h) => in_array('Authorization: Bearer ' . self::TOKEN, $h, true)),
                $this->isType('string'),
            )
            ->willReturn(new HttpResponse(200, json_encode([
                'success' => true,
                'result'  => ['abuse_rand' => 'rand-xyz'],
            ], JSON_THROW_ON_ERROR)));

        $client = new AbuseReportClient(self::TOKEN, $mock);
        $result = $client->submitReport(self::ACCOUNT, $this->phishingRequest());

        $this->assertInstanceOf(CreateReportResponse::class, $result);
        $this->assertSame('rand-xyz', $result->abuseRand);
    }

    public function testSubmitReportSerializesRequestBody(): void
    {
        $mock = $this->createMock(HttpTransportInterface::class);
        $mock->expects($this->once())
            ->method('request')
            ->with(
                'POST',
                $this->anything(),
                $this->anything(),
                $this->callback(fn($body) => str_contains($body, '"act":"abuse_phishing"')),
            )
            ->willReturn(new HttpResponse(200, json_encode([
                'success' => true,
                'result'  => ['abuse_rand' => 'r1'],
            ], JSON_THROW_ON_ERROR)));

        (new AbuseReportClient(self::TOKEN, $mock))->submitReport(self::ACCOUNT, $this->phishingRequest());
    }

    // --- getReport ---

    public function testGetReportCallsGetWithCorrectUrl(): void
    {
        $mock = $this->createMock(HttpTransportInterface::class);
        $mock->expects($this->once())
            ->method('request')
            ->with(
                'GET',
                'https://api.cloudflare.com/client/v4/accounts/' . self::ACCOUNT . '/abuse-reports/rep-1',
                $this->anything(),
                null,
            )
            ->willReturn(new HttpResponse(200, json_encode([
                'success' => true,
                'result'  => [
                    'id' => 'rep-1', 'domain' => 'd.com', 'status' => 'accepted',
                    'type' => 'abuse_phishing', 'cdate' => '2024-01-01T00:00:00Z',
                ],
            ], JSON_THROW_ON_ERROR)));

        $result = (new AbuseReportClient(self::TOKEN, $mock))->getReport(self::ACCOUNT, 'rep-1');

        $this->assertInstanceOf(ReportResponse::class, $result);
        $this->assertSame('rep-1', $result->id);
    }

    // --- listReports ---

    public function testListReportsCallsGetWithNoQueryWhenParamsNull(): void
    {
        $mock = $this->createMock(HttpTransportInterface::class);
        $mock->expects($this->once())
            ->method('request')
            ->with(
                'GET',
                'https://api.cloudflare.com/client/v4/accounts/' . self::ACCOUNT . '/abuse-reports',
                $this->anything(),
                null,
            )
            ->willReturn(new HttpResponse(200, json_encode([
                'success'     => true,
                'result'      => [],
                'result_info' => ['page' => 1, 'per_page' => 20, 'total_count' => 0],
            ], JSON_THROW_ON_ERROR)));

        $result = (new AbuseReportClient(self::TOKEN, $mock))->listReports(self::ACCOUNT);

        $this->assertInstanceOf(PaginatedResponse::class, $result);
    }

    public function testListReportsAppendsQueryParams(): void
    {
        $mock = $this->createMock(HttpTransportInterface::class);
        $mock->expects($this->once())
            ->method('request')
            ->with(
                'GET',
                $this->callback(fn($url) => str_contains($url, 'per_page=5') && str_contains($url, 'domain=evil.com')),
                $this->anything(),
                null,
            )
            ->willReturn(new HttpResponse(200, json_encode([
                'success'     => true,
                'result'      => [],
                'result_info' => ['page' => 1, 'per_page' => 5, 'total_count' => 0],
            ], JSON_THROW_ON_ERROR)));

        (new AbuseReportClient(self::TOKEN, $mock))->listReports(
            self::ACCOUNT,
            new ListReportsParams(perPage: 5, domain: 'evil.com'),
        );
    }

    public function testListReportsReturnsPaginatedItems(): void
    {
        $transport = $this->transport(200, [
            'success'     => true,
            'result'      => [
                ['id' => 'r1', 'domain' => 'd1.com', 'status' => 'accepted', 'type' => 'abuse_phishing', 'cdate' => '2024-01-01T00:00:00Z'],
                ['id' => 'r2', 'domain' => 'd2.com', 'status' => 'in_review', 'type' => 'abuse_general', 'cdate' => '2024-01-02T00:00:00Z'],
            ],
            'result_info' => ['page' => 2, 'per_page' => 10, 'total_count' => 42],
        ]);

        $result = (new AbuseReportClient(self::TOKEN, $transport))->listReports(self::ACCOUNT);

        $this->assertCount(2, $result->items);
        $this->assertSame(2, $result->page);
        $this->assertSame(10, $result->perPage);
        $this->assertSame(42, $result->totalCount);
        $this->assertSame('r1', $result->items[0]->id);
    }

    // --- listMitigations ---

    public function testListMitigationsCallsCorrectUrl(): void
    {
        $mock = $this->createMock(HttpTransportInterface::class);
        $mock->expects($this->once())
            ->method('request')
            ->with(
                'GET',
                $this->stringContains('/abuse-reports/rep-1/mitigations'),
                $this->anything(),
                null,
            )
            ->willReturn(new HttpResponse(200, json_encode([
                'success' => true,
                'result'  => [
                    ['id' => 'm1', 'status' => 'active', 'type' => 'legal_block'],
                ],
            ], JSON_THROW_ON_ERROR)));

        $result = (new AbuseReportClient(self::TOKEN, $mock))->listMitigations(self::ACCOUNT, 'rep-1');

        $this->assertCount(1, $result);
        $this->assertInstanceOf(MitigationResponse::class, $result[0]);
        $this->assertSame('m1', $result[0]->id);
    }

    // --- appealMitigations ---

    public function testAppealMitigationsCallsPostWithCommentsInBody(): void
    {
        $mock = $this->createMock(HttpTransportInterface::class);
        $mock->expects($this->once())
            ->method('request')
            ->with(
                'POST',
                $this->stringContains('/mitigations/appeal'),
                $this->anything(),
                $this->callback(fn($body) => str_contains($body, 'my appeal reason')),
            )
            ->willReturn(new HttpResponse(200, json_encode(['success' => true, 'result' => []], JSON_THROW_ON_ERROR)));

        (new AbuseReportClient(self::TOKEN, $mock))->appealMitigations(self::ACCOUNT, 'rep-1', 'my appeal reason');
    }

    // --- error handling ---

    public function testThrowsApiExceptionOn4xxResponse(): void
    {
        $transport = $this->transport(401, [
            'success' => false,
            'errors'  => [['message' => 'Invalid API token', 'code' => 10000]],
        ]);

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Invalid API token');

        (new AbuseReportClient(self::TOKEN, $transport))->getReport(self::ACCOUNT, 'r1');
    }

    public function testThrowsApiExceptionOn5xxResponse(): void
    {
        $transport = $this->transport(500, [
            'success' => false,
            'errors'  => [['message' => 'Internal server error', 'code' => 0]],
        ]);

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Internal server error');

        (new AbuseReportClient(self::TOKEN, $transport))->getReport(self::ACCOUNT, 'r1');
    }

    public function testThrowsApiExceptionWhenSuccessIsFalseWithOkStatus(): void
    {
        $transport = $this->transport(200, [
            'success' => false,
            'errors'  => [['message' => 'Validation error', 'code' => 1001]],
        ]);

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Validation error');

        (new AbuseReportClient(self::TOKEN, $transport))->getReport(self::ACCOUNT, 'r1');
    }

    public function testApiExceptionCarriesStatusCodeAndErrors(): void
    {
        $errors    = [['message' => 'Bad request', 'code' => 1001]];
        $transport = $this->transport(400, ['success' => false, 'errors' => $errors]);

        try {
            (new AbuseReportClient(self::TOKEN, $transport))->getReport(self::ACCOUNT, 'r1');
            $this->fail('Expected ApiException');
        } catch (ApiException $e) {
            $this->assertSame(400, $e->getStatusCode());
            $this->assertSame($errors, $e->getErrors());
        }
    }

    public function testThrowsApiExceptionOnInvalidJson(): void
    {
        $mock = $this->createMock(HttpTransportInterface::class);
        $mock->method('request')->willReturn(new HttpResponse(200, 'not-json'));

        $this->expectException(ApiException::class);
        $this->expectExceptionMessageMatches('/Failed to decode/');

        (new AbuseReportClient(self::TOKEN, $mock))->getReport(self::ACCOUNT, 'r1');
    }

    public function testFallsBackToDefaultErrorMessageWhenErrorsArrayEmpty(): void
    {
        $transport = $this->transport(400, ['success' => false, 'errors' => []]);

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('API request failed');

        (new AbuseReportClient(self::TOKEN, $transport))->getReport(self::ACCOUNT, 'r1');
    }
}
