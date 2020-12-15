<?php

if (!function_exists($func = 'scrypt')) {
    echo sprintf('FAIL: Function "%s" does not exist.', $func).PHP_EOL;
    exit(1);
}

exit(0);
