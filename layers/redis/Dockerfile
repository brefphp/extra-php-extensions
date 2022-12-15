ARG PHP_VERSION
FROM bref/build-php-$PHP_VERSION:1.7.14 AS ext

RUN pecl install --force redis
RUN cp `php-config --extension-dir`/redis.so /tmp/redis.so
RUN echo 'extension=/opt/bref-extra/redis.so' > /tmp/ext.ini

# Build the final image with just the files we need
FROM scratch

# Copy things we installed to the final image
COPY --from=ext /tmp/redis.so /opt/bref-extra/redis.so
COPY --from=ext /tmp/ext.ini /opt/bref/etc/php/conf.d/ext-redis.ini
