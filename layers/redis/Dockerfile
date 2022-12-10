# syntax = docker/dockerfile:1.4
ARG PHP_VERSION
FROM bref/build-php-$PHP_VERSION:2 AS ext

RUN <<eot bash -e
yum install -y php-redis
echo 'extension=redis.so' > /tmp/99-ext-redis.ini
echo 'extension=igbinary.so' > /tmp/00-ext-igbinary.ini
echo 'extension=msgpack.so' > /tmp/00-ext-msgpack.ini
eot


# Build the final image with just the files we need
FROM scratch

# Copy things we installed to the final image
COPY --link --from=ext /usr/lib64/liblzf.so* /opt/lib/
COPY --link --from=ext /usr/lib64/liblz4.so* /opt/lib/
COPY --link --from=ext /usr/lib64/php/modules/redis.so /opt/bref/extensions/
COPY --link --from=ext /usr/lib64/php/modules/igbinary.so /opt/bref/extensions/
COPY --link --from=ext /usr/lib64/php/modules/msgpack.so /opt/bref/extensions/
COPY --link --from=ext /tmp/*.ini /opt/bref/etc/php/conf.d/
