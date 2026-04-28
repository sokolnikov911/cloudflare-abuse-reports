# Cloudflare Abuse Reports API Client

PHP client for the [Cloudflare Abuse Reports API](https://developers.cloudflare.com/api/resources/abuse_reports).

## Requirements

- PHP 8.1 or higher
- `ext-curl`
- `ext-json`

## Installation

```bash
composer require sokolnikov911/cloudflare-abuse-reports
```

## Usage

### Setup

```php
use CloudflareAbuse\AbuseReportClient;

$client = new AbuseReportClient('your-cloudflare-api-token');
```

To customize the request timeout, pass your own transport:

```php
use CloudflareAbuse\AbuseReportClient;
use CloudflareAbuse\HttpTransport\CurlHttpTransport;

$client = new AbuseReportClient('your-api-token', new CurlHttpTransport(timeout: 60));
```

---

### Submit a report

Each report type has its own request class. All share common fields (`email`, `name`, `urls`) and add type-specific ones.

#### Phishing

```php
use CloudflareAbuse\Request\PhishingReportRequest;

$response = $client->submitReport('account-id', new PhishingReportRequest(
    email: 'reporter@example.com',
    name: 'Jane Doe',
    urls: "https://evil.example.com/login\nhttps://evil.example.com/steal",
    justification: 'This page impersonates a bank login form.',
));

echo $response->abuseRand; // report identifier
```

#### DMCA

```php
use CloudflareAbuse\Request\DmcaReportRequest;

$response = $client->submitReport('account-id', new DmcaReportRequest(
    email: 'rights@example.com',
    name: 'John Copyright',
    urls: 'https://infringing.example.com/stolen-content',
    originalWork: 'https://original.example.com/my-work',
    address1: '123 Main St',
    city: 'New York',
    state: 'NY',
    country: 'US',
));
```

> `signature`, `agree`, and notification fields are set automatically per DMCA requirements.

#### Trademark

```php
use CloudflareAbuse\Request\TrademarkReportRequest;

$response = $client->submitReport('account-id', new TrademarkReportRequest(
    email: 'legal@example.com',
    name: 'Legal Team',
    urls: 'https://infringing.example.com',
    trademarkNumber: 'TM12345',
    trademarkOffice: 'USPTO',
    trademarkSymbol: 'ACME',
    justification: 'Unauthorized use of our registered trademark.',
));
```

#### General abuse

```php
use CloudflareAbuse\Request\GeneralReportRequest;

$response = $client->submitReport('account-id', new GeneralReportRequest(
    email: 'security@example.com',
    name: 'Security Team',
    urls: 'https://malicious.example.com',
    justification: 'C2 server distributing malware.',
    destinationIps: '203.0.113.42',
    portsProtocols: '443/TCP',
    sourceIps: '198.51.100.1',
));
```

#### Other report types

| Class | `act` value |
|---|---|
| `ThreatReportRequest` | `abuse_threat` |
| `ChildrenReportRequest` | `abuse_children` |
| `RegistrarWhoisReportRequest` | `abuse_registrar_whois` |
| `NcseiReportRequest` | `abuse_ncsei` |

All constructors follow the same pattern: required fields first, then optional ones with defaults.

#### Notification options

For report types that support it, use `NotificationOption` to control whether the host and site owner are notified:

```php
use CloudflareAbuse\Enum\NotificationOption;
use CloudflareAbuse\Request\ThreatReportRequest;

new ThreatReportRequest(
    email: 'a@b.com',
    name: 'Alice',
    urls: 'https://threat.example.com',
    hostNotification: NotificationOption::SendAnon,
    ownerNotification: NotificationOption::None,
);
```

Available values: `Send`, `SendAnon`, `None`.

---

### Get a report

```php
$report = $client->getReport('account-id', 'report-id');

echo $report->id;
echo $report->domain;
echo $report->status; // "accepted" or "in_review"
echo $report->type;
echo $report->cdate;  // RFC 3339
```

---

### List reports

```php
use CloudflareAbuse\ListReportsParams;
use CloudflareAbuse\Enum\ReportStatus;
use CloudflareAbuse\Enum\ReportType;

$page = $client->listReports('account-id', new ListReportsParams(
    status: ReportStatus::InReview,
    type: ReportType::Phishing,
    domain: 'evil.example.com',
    perPage: 20,
    page: 1,
));

echo $page->totalCount;

foreach ($page->items as $report) {
    echo "{$report->id}: {$report->domain} ({$report->status})\n";
}
```

All `ListReportsParams` fields are optional. Omit the second argument to fetch with no filters.

---

### List mitigations

```php
$mitigations = $client->listMitigations('account-id', 'report-id');

foreach ($mitigations as $m) {
    echo "{$m->id}: {$m->type} — {$m->status}\n";
}
```

---

### Appeal a mitigation

```php
$client->appealMitigations('account-id', 'report-id', 'This content is not infringing.');
```

---

### Error handling

All methods throw `CloudflareAbuse\Exception\ApiException` on failure:

```php
use CloudflareAbuse\Exception\ApiException;

try {
    $report = $client->getReport('account-id', 'unknown-id');
} catch (ApiException $e) {
    echo $e->getMessage();    // Cloudflare error message
    echo $e->getStatusCode(); // HTTP status code
    print_r($e->getErrors()); // raw Cloudflare errors array
}
```

## License

MIT
