<?php

declare(strict_types=1);

namespace CloudflareAbuse;

use CloudflareAbuse\Exception\ApiException;
use CloudflareAbuse\HttpTransport\CurlHttpTransport;
use CloudflareAbuse\HttpTransport\HttpTransportInterface;
use CloudflareAbuse\Request\CreateReportRequest;
use CloudflareAbuse\Response\CreateReportResponse;
use CloudflareAbuse\Response\MitigationResponse;
use CloudflareAbuse\Response\PaginatedResponse;
use CloudflareAbuse\Response\ReportResponse;

class AbuseReportClient
{
    private const BASE_URL = 'https://api.cloudflare.com/client/v4';

    public function __construct(
        private readonly string $apiToken,
        private readonly HttpTransportInterface $transport = new CurlHttpTransport(),
    ) {
    }

    /**
     * @throws ApiException
     */
    public function submitReport(string $accountId, CreateReportRequest $request): CreateReportResponse
    {
        $type = $request->getType()->value;
        $data = $this->post("/accounts/{$accountId}/abuse-reports/{$type}", $request->toArray());

        return CreateReportResponse::fromArray($data['result'] ?? $data);
    }

    /**
     * @throws ApiException
     */
    public function getReport(string $accountId, string $reportId): ReportResponse
    {
        $data = $this->get("/accounts/{$accountId}/abuse-reports/{$reportId}");

        return ReportResponse::fromArray($data['result']);
    }

    /**
     * @return PaginatedResponse<ReportResponse>
     * @throws ApiException
     */
    public function listReports(string $accountId, ?ListReportsParams $params = null): PaginatedResponse
    {
        $query = $params?->toQueryParams() ?? [];
        $data  = $this->get("/accounts/{$accountId}/abuse-reports", $query);

        return PaginatedResponse::fromArray($data, ReportResponse::fromArray(...));
    }

    /**
     * @return MitigationResponse[]
     * @throws ApiException
     */
    public function listMitigations(string $accountId, string $reportId): array
    {
        $data = $this->get("/accounts/{$accountId}/abuse-reports/{$reportId}/mitigations");

        return array_map(MitigationResponse::fromArray(...), $data['result'] ?? []);
    }

    /**
     * @throws ApiException
     */
    public function appealMitigations(string $accountId, string $reportId, string $comments): array
    {
        $data = $this->post(
            "/accounts/{$accountId}/abuse-reports/{$reportId}/mitigations/appeal",
            ['comments' => $comments],
        );

        return $data['result'] ?? [];
    }

    private function get(string $path, array $query = []): array
    {
        return $this->request('GET', $path, $query);
    }

    private function post(string $path, array $body): array
    {
        return $this->request('POST', $path, [], $body);
    }

    private function request(string $method, string $path, array $query = [], array $body = []): array
    {
        $url = self::BASE_URL . $path;

        if (!empty($query)) {
            $url .= '?' . http_build_query($query);
        }

        $jsonBody = !empty($body) ? json_encode($body, JSON_THROW_ON_ERROR) : null;

        $response = $this->transport->request($method, $url, [
            'Authorization: Bearer ' . $this->apiToken,
            'Content-Type: application/json',
            'Accept: application/json',
        ], $jsonBody);

        $data = json_decode($response->body, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new ApiException('Failed to decode API response: ' . json_last_error_msg(), $response->statusCode);
        }

        if ($response->statusCode >= 400 || (isset($data['success']) && $data['success'] === false)) {
            $message = $data['errors'][0]['message'] ?? 'API request failed';
            throw new ApiException($message, $response->statusCode, $data['errors'] ?? null);
        }

        return $data;
    }
}
