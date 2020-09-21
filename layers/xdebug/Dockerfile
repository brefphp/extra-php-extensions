ARG PHP_VERSION
FROM bref/build-php-$PHP_VERSION AS ext

RUN pecl install xdebug
RUN cp `php-config --extension-dir`/xdebug.so /tmp/xdebug.so

# Build the final image from the lambci image that is close to the production environment
FROM scratch

# Copy things we installed to the final image
COPY --from=ext /tmp/xdebug.so /opt/bref-extra/xdebug.so
