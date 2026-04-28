<?php

declare(strict_types=1);

namespace CloudflareAbuse\Request;

use CloudflareAbuse\Enum\NotificationOption;
use CloudflareAbuse\Enum\ReportType;

class TrademarkReportRequest extends CreateReportRequest
{
    public function __construct(
        string $email,
        string $name,
        string $urls,
        public readonly string $trademarkNumber,
        public readonly string $trademarkOffice,
        public readonly string $trademarkSymbol,
        public readonly ?string $justification = null,
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
        return ReportType::Trademark;
    }

    public function toArray(): array
    {
        $data = parent::toArray();

        $data['trademark_number']   = $this->trademarkNumber;
        $data['trademark_office']   = $this->trademarkOffice;
        $data['trademark_symbol']   = $this->trademarkSymbol;
        $data['host_notification']  = $this->hostNotification->value;
        $data['owner_notification'] = $this->ownerNotification->value;

        if ($this->justification !== null) {
            $data['justification'] = $this->justification;
        }

        return $data;
    }
}
