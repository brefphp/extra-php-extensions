ARG PHP_VERSION
ARG BREF_VERSION
FROM bref/build-php-$PHP_VERSION:$BREF_VERSION AS ext

RUN MAKEFLAGS="-j $(nproc)" pecl install protobuf > /dev/null
RUN cp `php-config --extension-dir`/protobuf.so /tmp/protobuf.so
RUN strip --strip-debug /tmp/protobuf.so
RUN echo 'extension=protobuf.so' > /tmp/ext.ini

# Build the final image with just the files we need
FROM scratch

# Copy things we installed to the final image
COPY --from=ext /tmp/protobuf.so /opt/bref/extensions/protobuf.so
COPY --from=ext /tmp/ext.ini /opt/bref/etc/php/conf.d/ext-protobuf.ini
