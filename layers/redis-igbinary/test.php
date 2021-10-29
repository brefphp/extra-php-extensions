<?php
try {
    $redisSerializer = Redis::SERIALIZER_IGBINARY;
} catch (Error $e) {
    echo $e->getMessage().PHP_EOL;
    exit(1);
}
exit(0);
