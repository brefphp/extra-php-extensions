# syntax = docker/dockerfile:1.4
ARG PHP_VERSION
FROM bref/build-php-$PHP_VERSION:2 AS ext

RUN <<eot bash -e
yum install -y php-decimal
echo 'extension=decimal.so' > /tmp/ext-decimal.ini
eot

# Build the final image with just the files we need
FROM scratch

# Copy things we installed to the final image
# COPY --link --from=ext /usr/lib64/liblz4.so* /opt/lib/
COPY --link --from=ext /usr/lib64/libmpdec.so* /opt/lib/
COPY --link --from=ext /usr/lib64/php/modules/decimal.so /opt/bref/extensions/
COPY --link --from=ext /tmp/*.ini /opt/bref/etc/php/conf.d/
