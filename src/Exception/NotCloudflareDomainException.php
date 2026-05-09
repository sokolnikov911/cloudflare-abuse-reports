<?php

declare(strict_types=1);

namespace CloudflareAbuse\Exception;

/**
 * Thrown when Cloudflare rejects the submission as because domain is not a Cloudflare domain
 * (HTTP 200 with `result=error`, `err_code=url_not_orange`).
 */
class NotCloudflareDomainException extends ApiException
{
    public function __construct(
        string $message,
        private readonly ?string $domain = null,
        int $statusCode = 200,
    ) {
        parent::__construct($message, $statusCode);
    }

    public function getDomain(): ?string
    {
        return $this->domain;
    }

    /**
     * Extract the domain from the error message.
     */
    public static function extractDomainFromMessage(?string $message): ?string
    {
        if ($message === null || $message === '') {
            return null;
        }

        if (preg_match('/:\s*([a-z0-9.-]+\.[a-z]{2,})$/i', $message, $matches) === 1) {
            return $matches[1];
        }

        return null;
    }
}
