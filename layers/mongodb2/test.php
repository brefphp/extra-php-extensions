<?php

if (!class_exists($class = MongoDB\Driver\Manager::class)) {
    echo sprintf('FAIL: Class "%s" does not exist.', $class).PHP_EOL;
    exit(1);
}

exit(0);
