ARG PHP_VERSION
FROM bref/build-php-$PHP_VERSION:1.0.0 AS ext

RUN pecl install --force redis
RUN cp `php-config --extension-dir`/redis.so /tmp/redis.so

# Build the final image from the lambci image that is close to the production environment
FROM scratch

# Copy things we installed to the final image
COPY --from=ext /tmp/redis.so /opt/bref-extra/redis.so
