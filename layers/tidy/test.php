<?php

if (!function_exists($func = 'tidy_get_root')) {
    echo sprintf('FAIL: Function "%s" does not exist.', $func).PHP_EOL;
    exit(1);
}

exit(0);
