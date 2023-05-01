ARG PHP_VERSION
ARG BREF_VERSION
FROM bref/build-php-$PHP_VERSION:$BREF_VERSION AS ext

RUN pecl install ds
RUN cp `php-config --extension-dir`/ds.so /tmp/ds.so
RUN echo 'extension=ds.so' > /tmp/ext.ini

# Build the final image with just the files we need
FROM scratch

# Copy things we installed to the final image
COPY --from=ext /tmp/ds.so /opt/bref/extensions/ds.so
COPY --from=ext /tmp/ext.ini /opt/bref/etc/php/conf.d/ext-ds.ini
