<?php

declare(strict_types=1);

namespace CloudflareAbuse\HttpTransport;

use RuntimeException;

interface HttpTransportInterface
{
    /**
     * @param  list<string>  $headers  Lines in "Name: Value" format
     * @throws RuntimeException on network-level failure
     */
    public function request(string $method, string $url, array $headers, ?string $body = null): HttpResponse;
}
