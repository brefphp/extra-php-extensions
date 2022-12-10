ARG PHP_VERSION
FROM bref/build-php-$PHP_VERSION:1.7.14 AS ext

RUN pecl install msgpack
RUN cp `php-config --extension-dir`/msgpack.so /tmp/msgpack.so
RUN echo 'extension=/opt/bref-extra/msgpack.so' > /tmp/ext.ini

FROM scratch

# Copy things we installed to the final image
COPY --from=ext /tmp/msgpack.so /opt/bref-extra/msgpack.so
COPY --from=ext /tmp/ext.ini /opt/bref/etc/php/conf.d/ext-msgpack.ini
