<?php

declare(strict_types=1);

namespace CloudflareAbuse\Request;

use CloudflareAbuse\Enum\NotificationOption;
use CloudflareAbuse\Enum\ReportType;

class NcseiReportRequest extends CreateReportRequest
{
    public function __construct(
        string $email,
        string $name,
        string $urls,
        public readonly bool $ncseiSubjectRepresentation = false,
        public readonly ?string $country = null,
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
        return ReportType::Ncsei;
    }

    public function toArray(): array
    {
        $data = parent::toArray();

        $data['ncsei_subject_representation'] = $this->ncseiSubjectRepresentation;
        $data['host_notification']            = $this->hostNotification->value;
        $data['owner_notification']           = $this->ownerNotification->value;

        if ($this->country !== null) {
            $data['country'] = $this->country;
        }

        return $data;
    }
}
