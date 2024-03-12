<?php

if ( !extension_loaded($ext = 'parallel')) {
    echo sprintf('FAIL: Extension "%s" does not exist.', $ext).PHP_EOL;
    exit(1);
}

exit(0);
