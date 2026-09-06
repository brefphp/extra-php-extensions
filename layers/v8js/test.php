<?php

if (!extension_loaded('v8js')) {
    echo 'FAIL: v8js extension is not loaded.'.PHP_EOL;
    exit(1);
}

if (!class_exists(V8Js::class)) {
    echo 'FAIL: V8Js class does not exist.'.PHP_EOL;
    exit(1);
}

$v8js = new V8Js();
$result = $v8js->executeString('1 + 1');
if ($result !== 2) {
    echo sprintf('FAIL: Expected 2, got %s.', var_export($result, true)).PHP_EOL;
    exit(1);
}

exit(0);
