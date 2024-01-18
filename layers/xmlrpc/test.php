<?php

if (!function_exists($func = 'xmlrpc_server_create')) {
    echo sprintf('FAIL: Function "%s" does not exist.', $func).PHP_EOL;
    exit(1);
}

exit(0);
