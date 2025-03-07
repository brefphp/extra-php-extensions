ARG PHP_VERSION
ARG BREF_VERSION
FROM bref/build-php-$PHP_VERSION:$BREF_VERSION AS ext

RUN MAKEFLAGS="-j $(nproc)" pecl install opentelemetry-1.1.0 > /dev/null
RUN cp `php-config --extension-dir`/opentelemetry.so /tmp/opentelemetry.so
RUN strip --strip-debug /tmp/opentelemetry.so
RUN echo 'extension=opentelemetry.so' > /tmp/ext.ini

# Build the final image with just the files we need
FROM scratch

# Copy things we installed to the final image
COPY --from=ext /tmp/opentelemetry.so /opt/bref/extensions/opentelemetry.so
COPY --from=ext /tmp/ext.ini /opt/bref/etc/php/conf.d/ext-opentelemetry.ini
