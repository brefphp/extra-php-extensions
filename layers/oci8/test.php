<?php

if (! extension_loaded('oci8')) {
    echo 'FAIL: Extension "oci8" is not loaded.'.PHP_EOL;
    exit(1);
}

if (! function_exists($func = 'oci_connect')) {
    echo sprintf('FAIL: Function "%s" does not exist.', $func).PHP_EOL;
    exit(1);
}

$clientVersion = oci_client_version();
if (empty($clientVersion) || $clientVersion === '0.0.0.0.0') {
    echo 'FAIL: Extension loaded, but can\'t find Oracle Instant Client libraries.'.PHP_EOL;
    exit(1);
}

// Test live connection assuming db container is started (see docker-compose.yml for example).
// You may need to add --add-host=host.docker.internal:host-gateway to Makefile test: docker run if 
// you are on Linux
/*
$conn = oci_connect('SYSTEM', 'testing', 'host.docker.internal/FREE');
if (! $conn) {
    echo 'FAIL: Can\'t connect to Oracle database: '.oci_error()['message'].PHP_EOL;
    exit(1);
}

$stid = oci_parse($conn, 'SELECT 1 FROM DUAL');
if (! $stid) {
    echo 'FAIL: Can\'t parse SQL statement: '.oci_error($conn)['message'].PHP_EOL;
    oci_close($conn);
    exit(1);
}

if (! oci_execute($stid)) {
    echo 'FAIL: Can\'t execute SQL statement: '.oci_error($conn)['message'].PHP_EOL;
    oci_close($conn);
    exit(1);
}
$stid = null;
oci_close($conn);
*/

exit(0);
