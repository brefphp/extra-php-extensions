<?php

$input = hex2bin('f5fbc1c9');
$result = iconv('WINDOWS-1250', 'UTF-8', $input);

if ($result !== 'őűÁÉ') {
    echo 'FAIL: WINDOWS-1250 conversion is unavailable.', PHP_EOL;
    exit(1);
}

exit(0);
