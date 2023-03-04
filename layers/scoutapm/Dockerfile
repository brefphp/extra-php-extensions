ARG PHP_VERSION
ARG BREF_VERSION
FROM bref/build-php-$PHP_VERSION:$BREF_VERSION AS ext
RUN pecl install --force scoutapm
RUN cp `php-config --extension-dir`/scoutapm.so /tmp/scoutapm.so
RUN echo 'zend_extension=scoutapm.so' > /tmp/ext.ini

# Build the final image with just the files we need
FROM scratch

# Copy things we installed to the final image
COPY --from=ext /tmp/scoutapm.so /opt/bref/extensions/scoutapm.so
COPY --from=ext /tmp/ext.ini /opt/bref/etc/php/conf.d/ext-scoutapm.ini
