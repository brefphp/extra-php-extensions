<?php
echo "foo".PHP_EOL;
if (!class_exists(AMQPConnection::class)) {
    exit(1);
}

exit(0);
