ARG PHP_VERSION
FROM bref/build-php-$PHP_VERSION:1.0.2 AS ext

RUN pecl install --force mongodb
RUN cp `php-config --extension-dir`/mongodb.so /tmp/mongodb.so
RUN echo 'extension=/opt/bref-extra/mongodb.so' > /tmp/ext.ini

# Build the final image with just the files we need
FROM scratch

# Copy things we installed to the final image
COPY --from=ext /tmp/mongodb.so /opt/bref-extra/mongodb.so
COPY --from=ext /tmp/ext.ini /opt/bref/etc/php/conf.d/ext-mongodb.ini
