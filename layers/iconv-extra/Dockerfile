ARG PHP_VERSION
ARG BREF_VERSION

FROM bref/build-php-$PHP_VERSION:$BREF_VERSION AS ext

FROM scratch

COPY --from=ext /usr/lib64/gconv/ /opt/lib/gconv/
