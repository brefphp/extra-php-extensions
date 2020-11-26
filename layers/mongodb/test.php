<?php

if (!class_exists($class = MongoClient::class)) {
    echo sprintf('FAIL: Class "%s" does not exit.', $class).PHP_EOL;
    exit(1);
}

exit(0);
