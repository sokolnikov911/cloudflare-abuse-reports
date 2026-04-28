<?php

declare(strict_types=1);

namespace CloudflareAbuse\Enum;

enum ReportType: string
{
    case Dmca = 'abuse_dmca';
    case Trademark = 'abuse_trademark';
    case General = 'abuse_general';
    case Phishing = 'abuse_phishing';
    case Children = 'abuse_children';
    case Threat = 'abuse_threat';
    case RegistrarWhois = 'abuse_registrar_whois';
    case Ncsei = 'abuse_ncsei';
}
