<?php

declare(strict_types=1);

namespace CloudflareAbuse\Request;

use CloudflareAbuse\Enum\NotificationOption;
use CloudflareAbuse\Enum\ReportType;

class PhishingReportRequest extends CreateReportRequest
{
    public function __construct(
        string $email,
        string $name,
        string $urls,
        public readonly ?string $justification = null,
        public readonly ?string $originalWork = null,
        public readonly NotificationOption $hostNotification = NotificationOption::Send,
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
        return ReportType::Phishing;
    }

    public function toArray(): array
    {
        $data = parent::toArray();

        $data['host_notification']  = $this->hostNotification->value;
        $data['owner_notification'] = $this->ownerNotification->value;

        if ($this->justification !== null) {
            $data['justification'] = $this->justification;
        }
        if ($this->originalWork !== null) {
            $data['original_work'] = $this->originalWork;
        }

        return $data;
    }
}
