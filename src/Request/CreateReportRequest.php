<?php

declare(strict_types=1);

namespace CloudflareAbuse\Request;

use CloudflareAbuse\Enum\ReportType;

abstract class CreateReportRequest
{
    public function __construct(
        public readonly string $email,
        public readonly string $name,
        public readonly string $urls,
        public readonly ?string $company = null,
        public readonly ?string $comments = null,
        public readonly ?string $reportedCountry = null,
        public readonly ?string $reportedUserAgent = null,
        public readonly ?string $tele = null,
        public readonly ?string $title = null,
    ) {
    }

    abstract public function getType(): ReportType;

    public function toArray(): array
    {
        $data = [
            'act'    => $this->getType()->value,
            'email'  => $this->email,
            'email2' => $this->email,
            'name'   => $this->name,
            'urls'   => $this->urls,
        ];

        if ($this->company !== null) {
            $data['company'] = $this->company;
        }
        if ($this->comments !== null) {
            $data['comments'] = $this->comments;
        }
        if ($this->reportedCountry !== null) {
            $data['reported_country'] = $this->reportedCountry;
        }
        if ($this->reportedUserAgent !== null) {
            $data['reported_user_agent'] = $this->reportedUserAgent;
        }
        if ($this->tele !== null) {
            $data['tele'] = $this->tele;
        }
        if ($this->title !== null) {
            $data['title'] = $this->title;
        }

        return $data;
    }
}
