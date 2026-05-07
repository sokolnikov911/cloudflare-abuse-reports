<?php

declare(strict_types=1);

namespace CloudflareAbuse\Tests\Exception;

use CloudflareAbuse\Exception\DuplicateReportException;
use PHPUnit\Framework\TestCase;

class DuplicateReportExceptionTest extends TestCase
{
    public function testExposesDuplicateUrl(): void
    {
        $e = new DuplicateReportException('msg', 'https://example.com/foo');

        $this->assertSame('https://example.com/foo', $e->getDuplicateUrl());
    }

    public function testDuplicateUrlIsNullByDefault(): void
    {
        $e = new DuplicateReportException('msg');

        $this->assertNull($e->getDuplicateUrl());
    }

    public function testExtractUrlFromMessage(): void
    {
        $msg = 'You have already submitted this URL recently: https://w20.my-cima.net/watch.php?vid=a162c0f11';

        $this->assertSame(
            'https://w20.my-cima.net/watch.php?vid=a162c0f11',
            DuplicateReportException::extractUrlFromMessage($msg),
        );
    }

    public function testExtractUrlStripsTrailingPunctuation(): void
    {
        $msg = 'Already reported: https://example.com/page.';

        $this->assertSame(
            'https://example.com/page',
            DuplicateReportException::extractUrlFromMessage($msg),
        );
    }

    public function testExtractUrlReturnsNullWhenNoUrl(): void
    {
        $this->assertNull(DuplicateReportException::extractUrlFromMessage('no url here'));
        $this->assertNull(DuplicateReportException::extractUrlFromMessage(null));
        $this->assertNull(DuplicateReportException::extractUrlFromMessage(''));
    }
}
