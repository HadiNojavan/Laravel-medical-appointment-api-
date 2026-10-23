<?php
require 'vendor/autoload.php';

use Carbon\CarbonImmutable;
use RRule\RRule;

function overlaps(CarbonImmutable $aStart, CarbonImmutable $aEnd,
                  CarbonImmutable $bStart, CarbonImmutable $bEnd): bool
{
    return $aStart < $bEnd && $aEnd > $bStart;
}

$doctorTz    = 'Asia/Tehran';
$rrule       = 'FREQ=WEEKLY;BYDAY=SA,MO';
$validFrom   = '20261101';
$startTime   = '09:00';   // ساعت کاری دکتر (محلی)
$endTime     = '11:00';
$slotMinutes = 30;

// مرخصی مستقیم به UTC (معادل ۰۹:۴۵ تا ۱۰:۳۰ تهران)
$leaveStart = CarbonImmutable::parse('2026-11-07 06:15', 'UTC');
$leaveEnd   = CarbonImmutable::parse('2026-11-07 07:00', 'UTC');

$rule = new RRule("DTSTART;TZID=$doctorTz:{$validFrom}T000000\nRRULE:$rrule");
$from = new DateTime('2026-11-07 00:00:00', new DateTimeZone($doctorTz));
$to   = new DateTime('2026-11-09 23:59:59', new DateTimeZone($doctorTz));

foreach ($rule->getOccurrencesBetween($from, $to) as $date) {
    $day = $date->format('Y-m-d');

    // تنها جایی که timezone دکتر استفاده می‌شه، با تبدیل فوری به UTC
    $cursor = CarbonImmutable::parse("$day $startTime", $doctorTz)->utc();
    $end    = CarbonImmutable::parse("$day $endTime", $doctorTz)->utc();

    echo "== $day (UTC) ==" . PHP_EOL;

    while (true) {
        $slotEnd = $cursor->addMinutes($slotMinutes);
        if ($slotEnd > $end) break;

        $blocked = overlaps($cursor, $slotEnd, $leaveStart, $leaveEnd);

        echo $cursor->format('H:i') . ' - ' . $slotEnd->format('H:i')
            . ($blocked ? '  ✗ (مرخصی)' : '  ✓') . PHP_EOL;

        $cursor = $slotEnd;
    }
}
