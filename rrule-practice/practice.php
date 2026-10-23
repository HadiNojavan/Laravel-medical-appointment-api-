<?php
require 'vendor/autoload.php';

use RRule\RRule;

$rule = new RRule([
    'FREQ' => 'WEEKLY',
    'BYDAY' => ['SA', 'MO'],
    'DTSTART' => '2026-11-01',
    'COUNT' => 6,
]);

foreach ($rule as $date) {
    echo $date->format('Y-m-d l') . PHP_EOL;
}

echo PHP_EOL;
$rule = new RRule('DTSTART:20261101
RRULE:FREQ=WEEKLY;BYDAY=SA,SU,MO,TU,WE');

foreach ($rule->getOccurrencesBetween('2026-11-01', '2026-11-14') as $date) {
    echo $date->format('Y-m-d D') . PHP_EOL;
}

echo PHP_EOL;

try {
    new RRule('FREQ=BLAH');
} catch (\InvalidArgumentException $e) {
    echo 'Invalid: ' . $e->getMessage() . PHP_EOL;
}

$day = '2026-11-07';
$cursor = new DateTime("$day 09:00");
$end    = new DateTime("$day 11:00");
echo $cursor->format('Y-m-d H:i') . PHP_EOL;
echo $end->format('Y-m-d H:i') . PHP_EOL;
