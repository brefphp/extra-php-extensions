<?php

if (!function_exists($func = '\DDTrace\trace_method')) {
    echo sprintf('FAIL: Function "%s" does not exist.', $func).PHP_EOL;
    exit(1);
}

exit(0);
