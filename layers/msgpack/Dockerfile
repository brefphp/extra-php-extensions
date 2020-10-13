ARG PHP_VERSION
FROM bref/build-php-$PHP_VERSION AS ext

RUN pecl install msgpack
RUN cp `php-config --extension-dir`/msgpack.so /tmp/msgpack.so

# Build the final image from the lambci image that is close to the production environment
FROM scratch

# Copy things we installed to the final image
COPY --from=ext /tmp/msgpack.so /opt/bref-extra/msgpack.so
