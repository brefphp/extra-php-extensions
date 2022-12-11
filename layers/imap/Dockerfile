# syntax = docker/dockerfile:1.4
ARG PHP_VERSION
FROM bref/build-php-$PHP_VERSION:2 AS ext

RUN <<eot bash -e
yum install -y php-imap
echo 'extension=imap.so' > /tmp/ext.ini
eot

# Build the final image with just the files we need
FROM scratch

# Copy things we installed to the final image
COPY --from=ext /usr/lib64/libc-client.so* /opt/lib/
COPY --from=ext /usr/lib64/libpam.so* /opt/lib/
COPY --from=ext /usr/lib64/libaudit.so* /opt/lib/
COPY --from=ext /usr/lib64/libcap-ng.so* /opt/lib/
COPY --link --from=ext /usr/lib64/php/modules/imap.so /opt/bref/extensions/
COPY --link --from=ext /tmp/ext.ini /opt/bref/etc/php/conf.d/ext-imap.ini
