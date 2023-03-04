ARG PHP_VERSION
ARG BREF_VERSION
FROM bref/build-php-$PHP_VERSION:$BREF_VERSION AS ext

ENV LD_LIBRARY_PATH=/usr/lib:/usr/lib64:$LD_LIBRARY_PATH

RUN yum install -y libuuid-devel

RUN pecl install uuid
RUN cp `php-config --extension-dir`/uuid.so /tmp/uuid.so
RUN echo 'extension=uuid.so' > /tmp/ext.ini

RUN php /bref/lib-copy/copy-dependencies.php /tmp/uuid.so /tmp/extension-libs


# Build the final image with just the files we need
FROM scratch

# Copy things we installed to the final image
COPY --from=ext /tmp/uuid.so /opt/bref/extensions/uuid.so
COPY --from=ext /tmp/ext.ini /opt/bref/etc/php/conf.d/ext-uuid.ini
COPY --from=ext /tmp/extension-libs /opt/lib
