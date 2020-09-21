ARG PHP_VERSION
FROM bref/build-php-$PHP_VERSION AS ext

RUN pecl install scrypt
RUN cp `php-config --extension-dir`/scrypt.so /tmp/scrypt.so

FROM scratch

COPY --from=ext /tmp/scrypt.so /opt/bref-extra/scrypt.so
