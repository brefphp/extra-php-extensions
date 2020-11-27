<?php

if (!class_exists($class = Imagick::class)) {
    echo sprintf('FAIL: Class "%s" does not exit.', $class).PHP_EOL;
    exit(1);
}

foreach (['PNG', 'JPG', 'GIF', 'WEBP', 'HEIC'] as $format) {
    if (!\Imagick::queryFormats($format)) {
        echo sprintf('FAIL: Imagick does not support "%s".', $format).PHP_EOL;
        exit(1);
    }
}

exit(0);
