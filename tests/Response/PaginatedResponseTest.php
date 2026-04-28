<?php

declare(strict_types=1);

namespace CloudflareAbuse\Tests\Response;

use CloudflareAbuse\Response\PaginatedResponse;
use CloudflareAbuse\Response\ReportResponse;
use PHPUnit\Framework\TestCase;

class PaginatedResponseTest extends TestCase
{
    private function reportFixture(string $id): array
    {
        return ['id' => $id, 'domain' => 'd.com', 'status' => 'accepted', 'type' => 'abuse_general', 'cdate' => '2024-01-01T00:00:00Z'];
    }

    public function testFromArrayMapsItemsUsingMapper(): void
    {
        $data = [
            'result'      => [$this->reportFixture('r1'), $this->reportFixture('r2')],
            'result_info' => ['page' => 1, 'per_page' => 20, 'total_count' => 2],
        ];

        $response = PaginatedResponse::fromArray($data, ReportResponse::fromArray(...));

        $this->assertCount(2, $response->items);
        $this->assertInstanceOf(ReportResponse::class, $response->items[0]);
        $this->assertSame('r1', $response->items[0]->id);
        $this->assertSame('r2', $response->items[1]->id);
    }

    public function testFromArrayMapsPaginationInfo(): void
    {
        $data = [
            'result'      => [$this->reportFixture('r1')],
            'result_info' => ['page' => 3, 'per_page' => 10, 'total_count' => 55],
        ];

        $response = PaginatedResponse::fromArray($data, ReportResponse::fromArray(...));

        $this->assertSame(3, $response->page);
        $this->assertSame(10, $response->perPage);
        $this->assertSame(55, $response->totalCount);
    }

    public function testFromArrayDefaultsWhenResultInfoAbsent(): void
    {
        $data = ['result' => [$this->reportFixture('r1'), $this->reportFixture('r2')]];

        $response = PaginatedResponse::fromArray($data, ReportResponse::fromArray(...));

        $this->assertSame(1, $response->page);
        $this->assertSame(2, $response->perPage);
        $this->assertSame(2, $response->totalCount);
    }

    public function testFromArrayHandlesEmptyResult(): void
    {
        $data = ['result' => [], 'result_info' => ['page' => 1, 'per_page' => 20, 'total_count' => 0]];

        $response = PaginatedResponse::fromArray($data, ReportResponse::fromArray(...));

        $this->assertCount(0, $response->items);
        $this->assertSame(0, $response->totalCount);
    }
}
