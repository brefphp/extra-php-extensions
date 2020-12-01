<?php

// This test is for sqlsrv.so
if (!function_exists($func = 'sqlsrv_connect')) {
    echo sprintf('FAIL: Function "%s" does not exit.', $func).PHP_EOL;
    exit(1);
}

// This test is for pdo_sqlsrv.so
if (!extension_loaded('pdo_sqlsrv')) {
    echo sprintf('FAIL: PDO sqlsrv extension not loaded').PHP_EOL;
    exit(1);
}

exit(0);
