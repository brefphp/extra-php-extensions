ARG PHP_VERSION
ARG BREF_VERSION
FROM bref/build-php-$PHP_VERSION:$BREF_VERSION AS ext

RUN pecl install pcov
RUN cp `php-config --extension-dir`/pcov.so /tmp/pcov.so
RUN echo 'extension=pcov.so' > /tmp/ext.ini

# Build the final image with just the files we need
FROM scratch

# Copy things we installed to the final image
COPY --from=ext /tmp/pcov.so /opt/bref/extensions/pcov.so
COPY --from=ext /tmp/ext.ini /opt/bref/etc/php/conf.d/ext-pcov.ini
