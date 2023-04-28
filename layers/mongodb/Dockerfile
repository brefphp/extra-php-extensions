ARG PHP_VERSION
ARG BREF_VERSION
FROM bref/build-php-$PHP_VERSION:$BREF_VERSION AS ext

RUN pecl install --force mongodb
RUN cp `php-config --extension-dir`/mongodb.so /tmp/mongodb.so
RUN echo 'extension=mongodb.so' > /tmp/ext.ini

# Build the final image with just the files we need
FROM scratch

# Copy the things we installed to the final image
COPY --from=ext /tmp/mongodb.so /opt/bref/extensions/mongodb.so
COPY --from=ext /tmp/ext.ini /opt/bref/etc/php/conf.d/ext-mongodb.ini
