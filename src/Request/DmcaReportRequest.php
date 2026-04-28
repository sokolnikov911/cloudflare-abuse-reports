<?php

declare(strict_types=1);

namespace CloudflareAbuse\Request;

use CloudflareAbuse\Enum\ReportType;

class DmcaReportRequest extends CreateReportRequest
{
    public function __construct(
        string $email,
        string $name,
        string $urls,
        public readonly string $originalWork,
        public readonly string $address1,
        public readonly string $city,
        public readonly string $state,
        public readonly string $country,
        public readonly ?string $agentName = null,
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
        return ReportType::Dmca;
    }

    public function toArray(): array
    {
        $data = parent::toArray();

        $data['original_work']     = $this->originalWork;
        $data['address1']          = $this->address1;
        $data['city']              = $this->city;
        $data['state']             = $this->state;
        $data['country']           = $this->country;
        $data['signature']         = $this->name;
        $data['agree']             = 1;
        $data['host_notification'] = 'send';
        $data['owner_notification'] = 'send';

        if ($this->agentName !== null) {
            $data['agent_name'] = $this->agentName;
        }

        return $data;
    }
}
