# syntax = docker/dockerfile:1.4
ARG PHP_VERSION
FROM bref/build-php-$PHP_VERSION:2 AS ext

RUN <<eot bash -e
yum install -y php-ssh2
echo 'extension=ssh2.so' > /tmp/ext.ini
eot

# Build the final image with just the files we need
FROM scratch

# Copy things we installed to the final image
COPY --link --from=ext /opt/remi/libssh2/lib64/libssh2.so* /opt/lib/
COPY --link --from=ext /usr/lib64/php/modules/ssh2.so /opt/bref/extensions/
COPY --link --from=ext /tmp/ext.ini /opt/bref/etc/php/conf.d/ext-ssh2.ini
