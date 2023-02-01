# syntax = docker/dockerfile:1.4
ARG PHP_VERSION
FROM bref/build-php-$PHP_VERSION:1.7.14 AS ext

ARG TIDEWAYS_VERSION=5.5.14
# Versions: https://tideways.com/profiler/downloads
# Docs: https://app.tideways.io/o/Bref/Bref-Tideways/installation

RUN <<'END' bash -e
    mkdir -p /tmp/tideways
    cd /tmp/tideways
    export version=$(php -r "echo PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION;")
    curl -sSL "https://s3-eu-west-1.amazonaws.com/tideways/extension/${TIDEWAYS_VERSION}/tideways-php-${TIDEWAYS_VERSION}-x86_64.tar.gz" | tar -xz --strip-components=2 -C /tmp/tideways
    cp /tmp/tideways/tideways-php-${version}.so /tmp/tideways.so
    echo 'extension=/opt/bref-extra/tideways.so' > /tmp/ext-tideways.ini
END


# Build the final image with just the files we need
FROM scratch

# Copy things we installed to the final image
COPY --link --from=ext /tmp/tideways.so /opt/bref-extra/
COPY --link --from=ext /tmp/*.ini /opt/bref/etc/php/conf.d/
