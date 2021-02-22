<?php

if (!class_exists($class = Imagick::class)) {
    echo sprintf('FAIL: Class "%s" does not exist.', $class).PHP_EOL;
    exit(1);
}

foreach (['PNG', 'JPG', 'GIF', 'WEBP', 'HEIC'] as $format) {
    if (!\Imagick::queryFormats($format)) {
        echo sprintf('FAIL: Imagick does not support "%s".', $format).PHP_EOL;
        exit(1);
    }
}

exit(0);

// This test is disabled because it causes Docker to segfault. See https://github.com/brefphp/extra-php-extensions/pull/156
try {
    $draw = new \ImagickDraw();
    $draw->setFont(__DIR__.'/Arial.ttf');
    $draw->setFontSize(13);

    $canvas = new \Imagick();
    $canvas->queryFontMetrics($draw, 'hello');
} catch(\ImagickException $e) {
    echo sprintf('FAIL: Imagick cannot draw text "%s".', $e->getMessage()).PHP_EOL;
    exit(1);
} catch (\Throwable $e) {
    echo sprintf('FAIL: Imagick failed with "%s" exception: %s', get_class($e), $e->getMessage()).PHP_EOL;
    exit(1);
}

exit(0);
