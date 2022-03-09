<?php

// Provided by the extension
if (!function_exists($func = 'elastic_apm_send_to_server')) {
    echo sprintf('FAIL: Function "%s" does not exist.', $func) . PHP_EOL;
    exit(1);
}

// Provided by the included PHP package
if (!method_exists($class = 'ElasticApm', $method = 'beginCurrentTransaction')) {
    echo sprintf('FAIL: Method "%s::%s" does not exist.', $class, $method) . PHP_EOL;
    exit(1);
}

exit(0);
