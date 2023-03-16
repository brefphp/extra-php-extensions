ARG PHP_VERSION
ARG BREF_VERSION
FROM bref/build-php-$PHP_VERSION:$BREF_VERSION AS ext

RUN pecl install igbinary && \
    pecl install -D 'enable-redis-igbinary="yes"' redis && \
    cp `php-config --extension-dir`/igbinary.so /tmp/igbinary.so && \
    cp `php-config --extension-dir`/redis.so /tmp/redis.so && \
    echo 'extension=igbinary.so' > /tmp/ext-igbinary.ini && \
    echo 'extension=redis.so' > /tmp/ext-redis.ini

FROM scratch
COPY --from=ext /tmp/igbinary.so /opt/bref/extensions/igbinary.so
COPY --from=ext /tmp/ext-igbinary.ini /opt/bref/etc/php/conf.d/ext-igbinary.ini
COPY --from=ext /tmp/redis.so /opt/bref/extensions/redis.so
COPY --from=ext /tmp/ext-redis.ini /opt/bref/etc/php/conf.d/ext-redis.ini
