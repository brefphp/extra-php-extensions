# syntax = docker/dockerfile:1.4
ARG PHP_VERSION
FROM bref/build-php-$PHP_VERSION:2 AS ext

RUN <<eot bash -e
yum install -y php-gd
echo 'extension=gd.so' > /tmp/ext-gd.ini
eot

# Build the final image with just the files we need
FROM scratch

# Copy things we installed to the final image
COPY --link --from=ext /usr/lib64/libgd.so* /opt/lib/
COPY --link --from=ext /usr/lib64/php/modules/gd.so /opt/bref/extensions/
COPY --link --from=ext /tmp/*.ini /opt/bref/etc/php/conf.d/
