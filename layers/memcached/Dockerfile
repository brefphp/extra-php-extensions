# syntax = docker/dockerfile:1.4
ARG PHP_VERSION
FROM bref/build-php-$PHP_VERSION:2 AS ext

RUN <<eot bash -e
yum install -y php-memcached
echo 'extension=memcached.so' > /tmp/99-ext-memcached.ini
echo 'extension=igbinary.so' > /tmp/00-ext-igbinary.ini
echo 'extension=msgpack.so' > /tmp/00-ext-msgpack.ini
eot

# Build the final image with just the files we need
FROM scratch

# Copy things we installed to the final image
COPY --link --from=ext /opt/remi/libmemcached-awesome/lib64/libhashkit.so* /opt/lib/
COPY --link --from=ext /usr/lib64/libevent-*.so* /opt/lib/
COPY --link --from=ext /usr/lib64/libfastlz.so* /opt/lib/
COPY --link --from=ext /opt/remi/libmemcached-awesome/lib64/libmemcached*.so* /opt/lib/
COPY --link --from=ext /usr/lib64/php/modules/igbinary.so /opt/bref/extensions/
COPY --link --from=ext /usr/lib64/php/modules/msgpack.so /opt/bref/extensions/
COPY --link --from=ext /usr/lib64/php/modules/memcached.so /opt/bref/extensions/
COPY --link --from=ext /tmp/*.ini /opt/bref/etc/php/conf.d/
