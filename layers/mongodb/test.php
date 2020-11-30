<?php

if (!class_exists($class = MongoDB\Driver\Manager::class)) {
    echo sprintf('FAIL: Class "%s" does not exit.', $class).PHP_EOL;
    exit(1);
}

exit(0);
