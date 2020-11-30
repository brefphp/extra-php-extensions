<?php

exit(0);
// TODO enable this test

// This test is for sqlsrv.so
if (!function_exists($func = 'sqlsrv_connect')) {
    echo sprintf('FAIL: Function "%s" does not exit.', $func).PHP_EOL;
    exit(1);
}

exit(0);
