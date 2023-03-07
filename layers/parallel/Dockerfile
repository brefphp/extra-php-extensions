ARG PHP_VERSION
ARG BREF_VERSION
FROM bref/build-php-$PHP_VERSION:$BREF_VERSION AS ext

RUN pecl install parallel
RUN cp `php-config --extension-dir`/parallel.so /tmp/parallel.so
RUN echo 'extension=parallel.so' > /tmp/ext.ini

# Build the final image with just the files we need
FROM scratch

# Copy things we installed to the final image
COPY --from=ext /tmp/parallel.so /opt/bref/extensions/parallel.so
COPY --from=ext /tmp/ext.ini /opt/bref/etc/php/conf.d/ext-parallel.ini
