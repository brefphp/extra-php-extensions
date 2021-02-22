ARG PHP_VERSION
FROM bref/build-php-$PHP_VERSION:1.1.4 AS ext

RUN pecl install xdebug
RUN cp `php-config --extension-dir`/xdebug.so /tmp/xdebug.so
RUN echo 'zend_extension=/opt/bref-extra/xdebug.so' > /tmp/ext.ini

# Build the final image with just the files we need
FROM scratch

# Copy things we installed to the final image
COPY --from=ext /tmp/xdebug.so /opt/bref-extra/xdebug.so
COPY --from=ext /tmp/ext.ini /opt/bref/etc/php/conf.d/ext-xdebug.ini
