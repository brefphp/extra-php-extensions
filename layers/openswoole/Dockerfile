ARG CPU_PREFIX
ARG PHP_VERSION
ARG BREF_VERSION
FROM bref/${CPU_PREFIX}build-php-$PHP_VERSION:$BREF_VERSION AS ext

RUN pecl install openswoole-4.12.1
RUN cp `php-config --extension-dir`/openswoole.so /tmp/openswoole.so
RUN echo 'extension=openswoole.so' > /tmp/ext.ini

FROM scratch

COPY --from=ext /tmp/openswoole.so /opt/bref/extensions/openswoole.so
COPY --from=ext /tmp/ext.ini /opt/bref/etc/php/conf.d/ext-openswoole.ini
