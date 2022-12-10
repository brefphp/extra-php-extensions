ARG PHP_VERSION
FROM bref/build-php-$PHP_VERSION:1.7.14 AS ext

ENV LD_LIBRARY_PATH=/usr/lib:/usr/lib64:$LD_LIBRARY_PATH

RUN yum install -y libuuid-devel

RUN pecl install uuid
RUN cp `php-config --extension-dir`/uuid.so /tmp/uuid.so
RUN echo 'extension=/opt/bref-extra/uuid.so' > /tmp/ext.ini

# Build the final image with just the files we need
FROM scratch

# Copy things we installed to the final image
COPY --from=ext /usr/lib64/libuuid.so /opt/bref/lib64/llibuuid.so
COPY --from=ext /usr/include/uuid/uuid.h /opt/bref/include/uuid/uuid.h
COPY --from=ext /usr/lib64/pkgconfig/uuid.pc /opt/bref/lib64/pkgconfig/uuid.pc

COPY --from=ext /tmp/uuid.so /opt/bref-extra/uuid.so
COPY --from=ext /tmp/ext.ini /opt/bref/etc/php/conf.d/ext-uuid.ini
