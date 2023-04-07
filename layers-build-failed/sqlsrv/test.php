<?php

putenv('ODBCSYSINI=/opt/microsoft/conf/');

// This test is for sqlsrv.so
if (!function_exists($func = 'sqlsrv_connect')) {
    echo sprintf('FAIL: Function "%s" does not exist.', $func).PHP_EOL;
    exit(1);
}

// This test is for pdo_sqlsrv.so
if (!extension_loaded('pdo_sqlsrv')) {
    echo sprintf('FAIL: PDO sqlsrv extension not loaded').PHP_EOL;
    exit(1);
}

// This test attempts a connection, forcing MS odbc libraries to be loaded
if (!sqlsrv_connect('localhost', ['LoginTimeout' => 1])) {
    foreach (sqlsrv_errors() as $error) {
        if ($error['SQLSTATE'] == '01000') {
            echo sprintf('FAIL: sqlsrv extension library not loaded. %s', $error['message']).PHP_EOL;
            exit(1);
        }
    }
}

exit(0);
