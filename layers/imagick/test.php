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

try {
    $image = new \Imagick(__DIR__.'/test.pdf');
    $image->writeImage('/tmp/imagick-test.jpg');
    assert(file_exists('/tmp/imagick-test.jpg'));
} catch(\ImagickException $e) {
    echo sprintf('FAIL: Imagick cannot convert PDF "%s".', $e->getMessage()).PHP_EOL;
    exit(1);
} catch (\Throwable $e) {
    echo sprintf('FAIL: Imagick failed with "%s" exception: %s', get_class($e), $e->getMessage()).PHP_EOL;
    exit(1);
}

exit(0);
