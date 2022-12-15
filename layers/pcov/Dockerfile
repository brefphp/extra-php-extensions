ARG PHP_VERSION
FROM bref/build-php-$PHP_VERSION:1.7.14 AS ext

RUN pecl install pcov
RUN cp `php-config --extension-dir`/pcov.so /tmp/pcov.so
RUN echo 'extension=/opt/bref-extra/pcov.so' > /tmp/ext.ini

# Build the final image with just the files we need
FROM scratch

# Copy things we installed to the final image
COPY --from=ext /tmp/pcov.so /opt/bref-extra/pcov.so
COPY --from=ext /tmp/ext.ini /opt/bref/etc/php/conf.d/ext-pcov.ini
