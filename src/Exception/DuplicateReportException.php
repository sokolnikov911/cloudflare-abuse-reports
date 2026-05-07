<?php

declare(strict_types=1);

namespace CloudflareAbuse\Exception;

/**
 * Thrown when Cloudflare rejects the submission as a duplicate
 * (HTTP 200 with `result=error`, `err_code=dedupe`).
 */
class DuplicateReportException extends ApiException
{
    public function __construct(
        string $message,
        private readonly ?string $duplicateUrl = null,
        int $statusCode = 200,
    ) {
        parent::__construct($message, $statusCode);
    }

    public function getDuplicateUrl(): ?string
    {
        return $this->duplicateUrl;
    }

    /**
     * Best-effort extraction of the duplicate URL from Cloudflare's
     * `msg` field, e.g.:
     *   "You have already submitted this URL recently: https://example.com/foo"
     */
    public static function extractUrlFromMessage(?string $message): ?string
    {
        if ($message === null || $message === '') {
            return null;
        }

        if (preg_match('~https?://\S+~', $message, $matches) === 1) {
            return rtrim($matches[0], ".,;:'\"!?)");
        }

        return null;
    }
}
