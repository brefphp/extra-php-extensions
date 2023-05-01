ARG PHP_VERSION
ARG BREF_VERSION
FROM bref/build-php-$PHP_VERSION:$BREF_VERSION AS ext

RUN pecl install scrypt
RUN cp `php-config --extension-dir`/scrypt.so /tmp/scrypt.so
RUN echo 'extension=scrypt.so' > /tmp/ext.ini

FROM scratch

COPY --from=ext /tmp/scrypt.so /opt/bref/extensions/scrypt.so
COPY --from=ext /tmp/ext.ini /opt/bref/etc/php/conf.d/ext-scrypt.ini
