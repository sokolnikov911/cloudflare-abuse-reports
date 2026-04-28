<?php

declare(strict_types=1);

namespace CloudflareAbuse\Request;

use CloudflareAbuse\Enum\NotificationOption;
use CloudflareAbuse\Enum\ReportType;

class GeneralReportRequest extends CreateReportRequest
{
    public function __construct(
        string $email,
        string $name,
        string $urls,
        public readonly ?string $justification = null,
        public readonly NotificationOption $hostNotification = NotificationOption::Send,
        public readonly NotificationOption $ownerNotification = NotificationOption::Send,
        public readonly ?string $destinationIps = null,
        public readonly ?string $portsProtocols = null,
        public readonly ?string $sourceIps = null,
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
        return ReportType::General;
    }

    public function toArray(): array
    {
        $data = parent::toArray();

        $data['host_notification']  = $this->hostNotification->value;
        $data['owner_notification'] = $this->ownerNotification->value;

        if ($this->justification !== null) {
            $data['justification'] = $this->justification;
        }
        if ($this->destinationIps !== null) {
            $data['destination_ips'] = $this->destinationIps;
        }
        if ($this->portsProtocols !== null) {
            $data['ports_protocols'] = $this->portsProtocols;
        }
        if ($this->sourceIps !== null) {
            $data['source_ips'] = $this->sourceIps;
        }

        return $data;
    }
}
