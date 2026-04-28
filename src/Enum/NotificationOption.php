<?php

declare(strict_types=1);

namespace CloudflareAbuse\Enum;

enum NotificationOption: string
{
    case Send = 'send';
    case SendAnon = 'send-anon';
    case None = 'none';
}
