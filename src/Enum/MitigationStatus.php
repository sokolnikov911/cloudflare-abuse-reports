<?php

declare(strict_types=1);

namespace CloudflareAbuse\Enum;

enum MitigationStatus: string
{
    case Pending = 'pending';
    case Active = 'active';
    case InReview = 'in_review';
    case Cancelled = 'cancelled';
    case Removed = 'removed';
}
