#!/usr/bin/env php
<?php

/**
 * A small script to find what versions we should add to the docker images.
 * This script allows us to tag 1.5.6 and then 1.2.3 without breaking things.
 */

$configFile = __DIR__.'/config.json';
if ($argc < 2) {
    echo sprintf('Call with one argument "./%s 1.2.3"', basename(__FILE__));
    exit(1);
}

$version = $argv[1];
if (!preg_match('|([0-9]+)\.([0-9]+)\.([0-9]+)|', $version, $match)) {
    echo 'Please provide a version with 3 numbers in it. Like "1.12.34"';
    exit(1);
}

$update = false;
if (isset($argv[2])) {
    if ('--update' !== $argv[2]) {
        echo sprintf('Use "--update" if you want to update the config.json. Like "./%s 1.2.3 --update"', basename(__FILE__));
        exit(1);
    }
    $update = true;
}

$majorVersion = $match[1];
$config = json_decode((string)file_get_contents($configFile), true);
$latestTag = $config['latest_versions']['v'.$match[1]] ?? null;

if ($latestTag === null || -1 === version_compare($latestTag, $version)) {
    echo $match[1].PHP_EOL;
    if ($update) {
        $config['latest_versions']['v'.$match[1]] = $version;
        file_put_contents($configFile, json_encode($config, JSON_PRETTY_PRINT));
    }
}

echo $match[1].'.'.$match[2].PHP_EOL;
echo $match[1].'.'.$match[2].'.'.$match[3].PHP_EOL;
