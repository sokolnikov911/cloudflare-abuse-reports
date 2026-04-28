<?php

declare(strict_types=1);

namespace CloudflareAbuse\Request;

use CloudflareAbuse\Enum\NotificationOption;
use CloudflareAbuse\Enum\ReportType;

class RegistrarWhoisReportRequest extends CreateReportRequest
{
    public function __construct(
        string $email,
        string $name,
        string $urls,
        public readonly array $regWhoRequest = [],
        public readonly NotificationOption $ownerNotification = NotificationOption::Send,
        ?string $company = null,
        ?string $comments = null,
        ?string $reportedCountry = null,
        ?string $reportedUserAgent = null,
        ?string $tele = null,
        ?string $title = null,
    ) {
        parent::__construct($email, $name, $urls, $company, $comments, $reportedCountry, $reportedUserAgent, $tele, $title);
    }

    public function getType(): ReportType
    {
        return ReportType::RegistrarWhois;
    }

    public function toArray(): array
    {
        $data = parent::toArray();

        $data['owner_notification'] = $this->ownerNotification->value;

        if (!empty($this->regWhoRequest)) {
            $data['reg_who_request'] = $this->regWhoRequest;
        }

        return $data;
    }
}
