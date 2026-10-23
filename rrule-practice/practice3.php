<?php
require 'vendor/autoload.php';

use Carbon\CarbonImmutable;
use RRule\RRule;

$rrule       = 'FREQ=WEEKLY;BYDAY=SA,MO';
$validFrom   = '20261101';
$startTime   = '09:00';
$endTime     = '11:00';
$slotMinutes = 30;
$tz          = 'Asia/Tehran';

$rule = new RRule("DTSTART:$validFrom\nRRULE:$rrule");

foreach ($rule->getOccurrencesBetween('2026-11-01', '2026-11-14') as $date) {
    $day = $date->format('Y-m-d');

    // ساعت رو تو timezone دکتر بساز
    $cursor = CarbonImmutable::parse("$day $startTime", $tz);
    $end    = CarbonImmutable::parse("$day $endTime", $tz);

    echo "== $day ==" . PHP_EOL;

    while (true) {
        $slotEnd = $cursor->addMinutes($slotMinutes); // شیء جدید، $cursor دست‌نخورده

        if ($slotEnd > $end) break;

        // تبدیل به UTC فقط برای ذخیره/خروجی
        echo $cursor->format('Y-m-d H:i') . ' → '
            . $slotEnd->utc()->format('Y-m-d H:i') . ' (UTC)' . PHP_EOL;

        $cursor = $slotEnd;
    }
}
