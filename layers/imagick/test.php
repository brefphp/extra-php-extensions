<?php

if (!class_exists($class = Imagick::class)) {
    echo sprintf('FAIL: Class "%s" does not exist.', $class).PHP_EOL;
    exit(1);
}

$expected_formats = ['PNG', 'JPG', 'GIF', 'WEBP', 'HEIC', 'AVIF'];

foreach ( $expected_formats as $format) {
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
    $tmpdir = '/tmp/imagicktest';
    mkdir($tmpdir);

    foreach ($expected_formats as $format) {
        //for all files in the testfiles directory
        foreach (glob(__DIR__.'/testfiles/*') as $file) {
            $image = new \Imagick($file);
            $output_path = $tmpdir . '/' .pathinfo($file, PATHINFO_FILENAME) . '.' . strtolower($format);
            $image->writeImage($output_path);
            validateImageFile($output_path);
        }
    }

    // compare the size of the AVIF image with the original JPG, buggy builds may just copy the jpg 
    assert(filesize( $tmpdir . '/jpg_test.avif') < filesize(__DIR__.'/testfiles/jpg_test.jpg') * 0.9);

    // copy the output files to the testoutput directory, if it exists. Useful for local testing
    if (file_exists('/var/task/testoutput')){
        foreach (glob($tmpdir.'/*') as $file) {
            copy($file, '/var/task/testoutput/'.basename($file));
        }
    }

} catch(\ImagickException $e) {
    echo sprintf('FAIL: Imagick failed to write image "%s".', $e->getMessage()).PHP_EOL;
    exit(1);
} catch (\Throwable $e) {
    echo sprintf('FAIL: Imagick failed with "%s" exception: %s', get_class($e), $e->getMessage()).PHP_EOL;
    exit(1);
}

// some basic image validation
function validateImageFile($file) {
    assert(file_exists($file), 'File does not exist: ' . $file);
    assert(filesize($file) > 128, 'File size ( '. filesize($file) .' byte ) is < byte for ' . $file );
    // only for supported formats
    if (in_array(pathinfo($file, PATHINFO_EXTENSION), ['png', 'jpg', 'webp', 'avif']) && version_compare(phpversion(), '8.3', '>=')) {
        assert(getimagesize($file) !== false, 'getimagesize failed for ' . $file);
    }

}

exit(0);
