<?php

if (!class_exists($function = \Google\Protobuf\Any::class)) {
    echo sprintf('FAIL: Class "%s" does not exist.', $function) . PHP_EOL;
    exit(1);
}

exit(0);
