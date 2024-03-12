<?php

if (!function_exists($function = "OpenTelemetry\Instrumentation\hook")) {
    echo sprintf('FAIL: Function "%s" does not exist.', $function) . PHP_EOL;
    exit(1);
}

exit(0);
