<?php

declare(strict_types=1);

namespace CloudflareAbuse\HttpTransport;

use RuntimeException;

class CurlHttpTransport implements HttpTransportInterface
{
    public function __construct(
        private readonly int $timeout = 30,
    ) {
    }

    /** @param list<string> $headers */
    public function request(string $method, string $url, array $headers, ?string $body = null): HttpResponse
    {
        if ($url === '' || $method === '') {
            throw new RuntimeException('URL and method must not be empty');
        }

        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => $this->timeout,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_CUSTOMREQUEST  => $method,
            CURLOPT_HTTPHEADER     => $headers,
        ]);

        if ($body !== null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        }

        $responseBody = curl_exec($ch);
        $statusCode   = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError    = curl_error($ch);
        curl_close($ch);

        if ($responseBody === false) {
            throw new RuntimeException("cURL error: {$curlError}");
        }

        return new HttpResponse($statusCode, (string) $responseBody);
    }
}
