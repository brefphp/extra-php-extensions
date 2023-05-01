# syntax = docker/dockerfile:1.4
ARG PHP_VERSION
ARG BREF_VERSION
FROM bref/build-php-$PHP_VERSION:2 AS ext

ARG RELAY_VERSION=0.6.0

# Docs: https://relay.so/docs/1.x/installation#manual-installation

# Install extensions required by Relay
RUN pecl install igbinary msgpack && \
    cp `php-config --extension-dir`/igbinary.so /tmp/igbinary.so && \
    cp `php-config --extension-dir`/msgpack.so /tmp/msgpack.so && \
    echo 'extension=igbinary.so' > /tmp/ext-igbinary.ini && \
    echo 'extension=msgpack.so' > /tmp/ext-msgpack.ini

# Install system dependencies needed by Relay
ARG ZSTD_VERSION=1.5.2
RUN <<'END' bash -e
    mkdir -p /tmp/zstd
    cd /tmp/zstd
    curl -sSL "https://github.com/facebook/zstd/archive/refs/tags/v${ZSTD_VERSION}.tar.gz" | tar -xz --strip-components=1 -C .
    make -j$(nproc)
    # Install the library
    cp lib/libzstd.so* /opt/lib
END

RUN <<'END' bash -e
    export php_version=$(php-config --version | cut -c -3)
    mkdir -p /tmp/relay
    curl -sSL "https://builds.r2.relay.so/v$RELAY_VERSION/relay-v$RELAY_VERSION-php$php_version-centos7-x86-64.tar.gz" | tar -xz --strip-components=1 -C /tmp/relay
    sed -i "s/00000000-0000-0000-0000-000000000000/$(cat /proc/sys/kernel/random/uuid)/" /tmp/relay/relay-pkg.so
    cp /tmp/relay/relay-pkg.so /tmp/relay.so
    echo 'extension=relay.so' > /tmp/ext-relay.ini
END

RUN php /bref/lib-copy/copy-dependencies.php /tmp/relay.so /tmp/extension-libs
RUN php /bref/lib-copy/copy-dependencies.php /tmp/igbinary.so /tmp/extension-libs
RUN php /bref/lib-copy/copy-dependencies.php /tmp/msgpack.so /tmp/extension-libs


# Build the final image with just the files we need
FROM scratch

# Copy things we installed to the final image
COPY --link --from=ext /tmp/relay.so /opt/bref/extensions/
COPY --link --from=ext /tmp/igbinary.so /opt/bref/extensions/
COPY --link --from=ext /tmp/msgpack.so /opt/bref/extensions/
COPY --link --from=ext /tmp/*.ini /opt/bref/etc/php/conf.d/
COPY --link --from=ext /tmp/extension-libs /opt/lib
