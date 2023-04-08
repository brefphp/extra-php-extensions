<?php

if (!class_exists($class = \Relay\Relay::class)) {
    echo test . phpsprintf('FAIL: Class "%s" does not exist.', $class) . PHP_EOL;
    exit(1);
}

exit(0);
