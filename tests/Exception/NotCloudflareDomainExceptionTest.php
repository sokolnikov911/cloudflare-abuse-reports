<?php

declare(strict_types=1);

namespace CloudflareAbuse\Tests\Exception;

use CloudflareAbuse\Exception\ApiException;
use CloudflareAbuse\Exception\NotCloudflareDomainException;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class NotCloudflareDomainExceptionTest extends TestCase
{
    public function testIsApiException(): void
    {
        $e = new NotCloudflareDomainException('msg');

        $this->assertInstanceOf(ApiException::class, $e);
        $this->assertInstanceOf(RuntimeException::class, $e);
    }

    public function testExposesDomain(): void
    {
        $e = new NotCloudflareDomainException('msg', 'p.holtfilm5.sbs');

        $this->assertSame('p.holtfilm5.sbs', $e->getDomain());
    }

    public function testDomainIsNullByDefault(): void
    {
        $e = new NotCloudflareDomainException('msg');

        $this->assertNull($e->getDomain());
    }

    public function testDefaultsStatusCodeTo200(): void
    {
        $e = new NotCloudflareDomainException('msg');

        $this->assertSame(200, $e->getStatusCode());
    }

    public function testCustomStatusCodeIsExposed(): void
    {
        $e = new NotCloudflareDomainException('msg', 'p.holtfilm5.sbs', 422);

        $this->assertSame(422, $e->getStatusCode());
    }

    public function testMessageIsForwardedToParent(): void
    {
        $msg = 'A URL contains a domain that is not active on Cloudflare: p.holtfilm5.sbs';

        $e = new NotCloudflareDomainException($msg, 'p.holtfilm5.sbs');

        $this->assertSame($msg, $e->getMessage());
    }

    public function testExtractDomainFromMessage(): void
    {
        $msg = 'A URL contains a domain that is not active on Cloudflare: p.holtfilm5.sbs';

        $this->assertSame(
            'p.holtfilm5.sbs',
            NotCloudflareDomainException::extractDomainFromMessage($msg),
        );
    }

    public function testExtractDomainFromMessageWithApexDomain(): void
    {
        $msg = 'A URL contains a domain that is not active on Cloudflare: example.com';

        $this->assertSame(
            'example.com',
            NotCloudflareDomainException::extractDomainFromMessage($msg),
        );
    }

    public function testExtractDomainFromMessageWithMultiLevelSubdomain(): void
    {
        $msg = 'A URL contains a domain that is not active on Cloudflare: foo.bar.example.co.uk';

        $this->assertSame(
            'foo.bar.example.co.uk',
            NotCloudflareDomainException::extractDomainFromMessage($msg),
        );
    }

    public function testExtractDomainFromMessageWithHyphenAndDigits(): void
    {
        $msg = 'A URL contains a domain that is not active on Cloudflare: my-site-123.example.org';

        $this->assertSame(
            'my-site-123.example.org',
            NotCloudflareDomainException::extractDomainFromMessage($msg),
        );
    }

    public function testExtractDomainIsCaseInsensitive(): void
    {
        $msg = 'A URL contains a domain that is not active on Cloudflare: Example.COM';

        $this->assertSame(
            'Example.COM',
            NotCloudflareDomainException::extractDomainFromMessage($msg),
        );
    }

    public function testExtractDomainReturnsNullWhenNoMatch(): void
    {
        $this->assertNull(NotCloudflareDomainException::extractDomainFromMessage('no domain here'));
    }

    public function testExtractDomainReturnsNullForNullInput(): void
    {
        $this->assertNull(NotCloudflareDomainException::extractDomainFromMessage(null));
    }

    public function testExtractDomainReturnsNullForEmptyString(): void
    {
        $this->assertNull(NotCloudflareDomainException::extractDomainFromMessage(''));
    }

    public function testExtractDomainReturnsNullWhenColonMissing(): void
    {
        $this->assertNull(
            NotCloudflareDomainException::extractDomainFromMessage(
                'A URL contains a domain p.holtfilm5.sbs that is not active on Cloudflare',
            ),
        );
    }

    public function testExtractDomainReturnsNullForSingleLabel(): void
    {
        $this->assertNull(
            NotCloudflareDomainException::extractDomainFromMessage(
                'A URL contains a domain that is not active on Cloudflare: localhost',
            ),
        );
    }
}
