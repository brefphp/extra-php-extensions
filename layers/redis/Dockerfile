ARG PHP_VERSION
ARG BREF_VERSION
FROM bref/build-php-$PHP_VERSION:$BREF_VERSION AS ext

RUN pecl install --force redis
RUN cp `php-config --extension-dir`/redis.so /tmp/redis.so
RUN echo 'extension=redis.so' > /tmp/ext.ini

RUN php /bref/lib-copy/copy-dependencies.php /tmp/relay.so /tmp/extension-libs


# Build the final image with just the files we need
FROM scratch

# Copy things we installed to the final image
COPY --from=ext /tmp/redis.so /opt/bref/extensions/redis.so
COPY --from=ext /tmp/ext.ini /opt/bref/etc/php/conf.d/ext-redis.ini
COPY --from=ext /tmp/extension-libs /opt/lib
