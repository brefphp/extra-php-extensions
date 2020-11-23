ARG PHP_VERSION
FROM bref/build-php-$PHP_VERSION:latest AS ext

RUN pecl install igbinary
RUN cp `php-config --extension-dir`/igbinary.so /tmp/igbinary.so

# Build the final image from the lambci image that is close to the production environment
FROM scratch

# Copy things we installed to the final image
COPY --from=ext /tmp/igbinary.so /opt/bref-extra/igbinary.so
