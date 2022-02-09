ARG PHP_VERSION
FROM bref/build-php-$PHP_VERSION:1.3.3 AS ext

RUN pecl install scrypt
RUN cp `php-config --extension-dir`/scrypt.so /tmp/scrypt.so
RUN echo 'extension=/opt/bref-extra/scrypt.so' > /tmp/ext.ini

FROM scratch

COPY --from=ext /tmp/scrypt.so /opt/bref-extra/scrypt.so
COPY --from=ext /tmp/ext.ini /opt/bref/etc/php/conf.d/ext-scrypt.ini
