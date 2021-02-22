<?php

if (!function_exists($func = 'newrelic_ignore_transaction')) {
    echo sprintf('FAIL: Function "%s" does not exist.', $func).PHP_EOL;
    exit(1);
}

exit(0);
