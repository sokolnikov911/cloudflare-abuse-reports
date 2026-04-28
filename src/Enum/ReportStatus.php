<?php

declare(strict_types=1);

namespace CloudflareAbuse\Enum;

enum ReportStatus: string
{
    case Accepted = 'accepted';
    case InReview = 'in_review';
}
