ARG PHP_VERSION
ARG BREF_VERSION
FROM bref/build-php-$PHP_VERSION:$BREF_VERSION AS ext

RUN pecl install brotli-0.15.0
RUN cp `php-config --extension-dir`/brotli.so /tmp/brotli.so
RUN echo 'extension=brotli.so' > /tmp/ext.ini

# Build the final image with just the files we need
FROM scratch

# Copy the things we installed to the final image
COPY --from=ext /tmp/brotli.so /opt/bref/extensions/brotli.so
COPY --from=ext /tmp/ext.ini /opt/bref/etc/php/conf.d/ext-brotli.ini
