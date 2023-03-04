ARG PHP_VERSION
ARG BREF_VERSION
FROM bref/build-php-$PHP_VERSION:$BREF_VERSION AS ext

RUN pecl install msgpack
RUN cp `php-config --extension-dir`/msgpack.so /tmp/msgpack.so
RUN echo 'extension=msgpack.so' > /tmp/ext.ini

FROM scratch

# Copy things we installed to the final image
COPY --from=ext /tmp/msgpack.so /opt/bref/extensions/msgpack.so
COPY --from=ext /tmp/ext.ini /opt/bref/etc/php/conf.d/ext-msgpack.ini
