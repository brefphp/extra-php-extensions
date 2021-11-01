ARG PHP_VERSION
FROM bref/build-php-$PHP_VERSION:1.2.13 AS ext

RUN pecl install igbinary && \
    pecl install -D 'enable-redis-igbinary="yes" enable-redis-lzf="no" enable-redis-zstd="no"' redis && \
    cp `php-config --extension-dir`/igbinary.so /tmp/igbinary.so && \
    cp `php-config --extension-dir`/redis.so /tmp/redis.so && \
    echo 'extension=/opt/bref-extra/igbinary.so' > /tmp/ext-igbinary.ini && \
    echo 'extension=/opt/bref-extra/redis.so' > /tmp/ext-redis.ini

FROM scratch
COPY --from=ext /tmp/igbinary.so /opt/bref-extra/igbinary.so
COPY --from=ext /tmp/ext-igbinary.ini /opt/bref/etc/php/conf.d/ext-igbinary.ini
COPY --from=ext /tmp/redis.so /opt/bref-extra/redis.so
COPY --from=ext /tmp/ext-redis.ini /opt/bref/etc/php/conf.d/ext-redis.ini
