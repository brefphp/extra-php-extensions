ARG PHP_VERSION
FROM bref/build-php-$PHP_VERSION:1.7.14 AS ext
RUN pecl install --force scoutapm
RUN cp `php-config --extension-dir`/scoutapm.so /tmp/scoutapm.so
RUN echo 'zend_extension=/opt/bref-extra/scoutapm.so' > /tmp/ext.ini

# Build the final image with just the files we need
FROM scratch

# Copy things we installed to the final image
COPY --from=ext /tmp/scoutapm.so /opt/bref-extra/scoutapm.so
COPY --from=ext /tmp/ext.ini /opt/bref/etc/php/conf.d/ext-scoutapm.ini
