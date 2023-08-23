ARG PHP_VERSION
ARG BREF_VERSION
FROM bref/build-php-$PHP_VERSION:$BREF_VERSION AS ext

RUN MAKEFLAGS="-j $(nproc)" && pecl install grpc-1.57.0 > /dev/null
RUN cp `php-config --extension-dir`/grpc.so /tmp/grpc.so
RUN strip --strip-debug /tmp/grpc.so
RUN echo 'extension=grpc.so' > /tmp/ext.ini

# Build the final image with just the files we need
FROM scratch

# Copy things we installed to the final image
COPY --from=ext /tmp/grpc.so /opt/bref/extensions/grpc.so
COPY --from=ext /tmp/ext.ini /opt/bref/etc/php/conf.d/ext-grpc.ini
