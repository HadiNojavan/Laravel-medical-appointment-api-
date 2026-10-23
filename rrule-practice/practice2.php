<?php
require 'vendor/autoload.php';

use RRule\RRule;

$rrule        = 'FREQ=WEEKLY;BYDAY=SA,MO';
$validFrom    = '20261101';
$startTime    = '09:00';
$endTime      = '11:00';
$slotMinutes  = 30;

$rule = new RRule("DTSTART:$validFrom\nRRULE:$rrule");

foreach ($rule->getOccurrencesBetween('2026-11-01', '2026-11-14') as $date) {
    $day    = $date->format('Y-m-d');
    $cursor = new DateTime("$day $startTime");
    $end    = new DateTime("$day $endTime");

    echo "== " . $date->format('Y-m-d l') . " ==" . PHP_EOL;

    while (true) {
        $slotEnd = (clone $cursor)->modify("+$slotMinutes minutes");
        if ($slotEnd > $end) break;

        echo $cursor->format('H:i') . ' - ' . $slotEnd->format('H:i') . PHP_EOL;
        $cursor = $slotEnd;
    }
}
