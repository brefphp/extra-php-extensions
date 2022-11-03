<?php

if (!class_exists($class = Decimal\Decimal::class)) {
    echo sprintf('FAIL: Class "%s" does not exist.', $class).PHP_EOL;
    exit(1);
}

try {
    $d = new \Decimal\Decimal(5);
    $d->isZero();
} catch (\Throwable $e) {
    echo sprintf('FAIL: Decimal failed with "%s" exception: %s', get_class($e), $e->getMessage()).PHP_EOL;
    exit(1);
}

exit(0);
