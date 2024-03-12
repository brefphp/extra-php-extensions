<?php

if (!function_exists($func = 'snmp2_real_walk')) {
    echo sprintf('FAIL: Function "%s" does not exist.', $func).PHP_EOL;
    exit(1);
}

exit(0);
