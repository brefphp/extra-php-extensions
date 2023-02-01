# syntax = docker/dockerfile:1.4
ARG PHP_VERSION
FROM bref/build-php-$PHP_VERSION:1.7.14 AS ext

ARG RELAY_VERSION=0.6.0

# Docs: https://relay.so/docs/1.x/installation#manual-installation

# Install extensions needed by Relay
RUN pecl install igbinary msgpack && \
    cp `php-config --extension-dir`/igbinary.so /tmp/igbinary.so && \
    cp `php-config --extension-dir`/msgpack.so /tmp/msgpack.so && \
    echo 'extension=/opt/bref-extra/igbinary.so' > /tmp/ext-igbinary.ini && \
    echo 'extension=/opt/bref-extra/msgpack.so' > /tmp/ext-msgpack.ini

# Install system dependencies needed by Relay
ARG ZSTD_VERSION=1.5.2
RUN <<'END' bash -e
    mkdir -p /tmp/zstd
    cd /tmp/zstd
    curl -sSL "https://github.com/facebook/zstd/archive/refs/tags/v${ZSTD_VERSION}.tar.gz" | tar -xz --strip-components=1 -C .
    make -j$(nproc)
END

RUN <<'END' bash -e
    mkdir -p /tmp/relay
    cd /tmp/relay
    export version=$(php -r "echo PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION;")
    curl -sSL "https://builds.r2.relay.so/v$RELAY_VERSION/relay-v$RELAY_VERSION-php$version-centos7-x86-64.tar.gz" | tar -xz --strip-components=1 -C /tmp/relay
    sed -i "s/00000000-0000-0000-0000-000000000000/$(cat /proc/sys/kernel/random/uuid)/" /tmp/relay/relay-pkg.so
    cp /tmp/relay/relay-pkg.so /tmp/relay.so
    echo 'extension=/opt/bref-extra/relay.so' > /tmp/ext-relay.ini
END


# Build the final image with just the files we need
FROM scratch

# Copy things we installed to the final image
#COPY --link --from=ext /usr/lib64/libpthread.so.0 /opt/lib/ # already in AL2
COPY --link --from=ext /opt/bref/lib64/libssl.so.1.1* /opt/lib/
COPY --link --from=ext /opt/bref/lib64/libcrypto.so.1.1* /opt/lib/
COPY --link --from=ext /tmp/zstd/lib/libzstd.so.1 /opt/lib/
COPY --link --from=ext /usr/lib64/liblz4.so* /opt/lib/
#COPY --link --from=ext /usr/lib64/libanl.so.1 /opt/lib/ # already in AL2
#COPY --link --from=ext /usr/lib64/libc.so.6 /opt/lib/ # already in AL2

COPY --link --from=ext /tmp/relay.so /opt/bref-extra/
COPY --link --from=ext /tmp/igbinary.so /opt/bref-extra/
COPY --link --from=ext /tmp/msgpack.so /opt/bref-extra/
COPY --link --from=ext /tmp/*.ini /opt/bref/etc/php/conf.d/
