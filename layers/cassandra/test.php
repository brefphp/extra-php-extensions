<?php

if (!class_exists($class = Cassandra\Timestamp::class)) {
    echo sprintf('FAIL: Class "%s" does not exit.', $class).PHP_EOL;
    exit(1);
}

exit(0);
