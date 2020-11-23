ARG PHP_VERSION
FROM bref/build-php-$PHP_VERSION:latest AS ext

RUN pecl install pcov
RUN cp `php-config --extension-dir`/pcov.so /tmp/pcov.so

# Build the final image from the lambci image that is close to the production environment
FROM scratch

# Copy things we installed to the final image
COPY --from=ext /tmp/pcov.so /opt/bref-extra/pcov.so
