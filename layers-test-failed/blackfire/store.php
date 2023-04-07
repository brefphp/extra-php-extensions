#!/usr/bin/env php
<?php

if ($argc !== 3) {
    echo 'Usage: php ./store.php key value';
    exit(1);
}

$file = __DIR__.'/versions.json';
$data = json_decode(file_get_contents($file), true);
$data[$argv[1]] = $argv[2];

file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));

exit(0);