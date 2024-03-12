ARG PHP_VERSION
ARG BREF_VERSION
FROM bref/build-php-$PHP_VERSION:$BREF_VERSION AS ext

RUN MAKEFLAGS="-j $(nproc)" && pecl install xmlrpc-1.0.0RC3 > /dev/null
RUN cp `php-config --extension-dir`/xmlrpc.so /tmp/xmlrpc.so
RUN strip --strip-debug /tmp/xmlrpc.so
RUN echo 'extension=xmlrpc.so' > /tmp/ext.ini

# Build the final image with just the files we need
FROM scratch

# Copy things we installed to the final image
COPY --from=ext /tmp/xmlrpc.so /opt/bref/extensions/xmlrpc.so
# This is exclusive for php bref lambda
COPY --from=ext /tmp/xmlrpc.so /opt/bref/extensions/php_xmlrpc.so
COPY --from=ext /tmp/ext.ini /opt/bref/etc/php/conf.d/ext-xmlrpc.ini
