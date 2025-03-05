# syntax = docker/dockerfile:1.4
ARG PHP_VERSION
ARG BREF_VERSION
ARG TIDEWAYS_VERSION=5.18.6

FROM ghcr.io/tideways/php:$TIDEWAYS_VERSION AS tideways
FROM bref/build-php-$PHP_VERSION:$BREF_VERSION AS ext

# Versions: https://tideways.com/profiler/downloads
# Docs: https://app.tideways.io/o/Bref/Bref-Tideways/installation

COPY --from=tideways /tideways/ /tideways/
RUN set -ex; \
    cp "$(php /tideways/get-ext-path.php)" /tmp/tideways.so; \
    echo "extension=tideways.so" > /tmp/ext-tideways.ini;

# Build the final image with just the files we need
FROM scratch

# Copy things we installed to the final image
COPY --link --from=ext /tmp/tideways.so /opt/bref/extensions/
COPY --link --from=ext /tmp/*.ini /opt/bref/etc/php/conf.d/
