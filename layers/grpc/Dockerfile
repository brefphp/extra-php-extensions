ARG PHP_VERSION
FROM bref/build-php-$PHP_VERSION:1.7.14 AS ext

RUN MAKEFLAGS="-j $(nproc)" && pecl install grpc > /dev/null
RUN cp `php-config --extension-dir`/grpc.so /tmp/grpc.so
RUN strip --strip-debug /tmp/grpc.so
RUN echo 'extension=/opt/bref-extra/grpc.so' > /tmp/ext.ini

# Build the final image with just the files we need
FROM scratch

# Copy things we installed to the final image
COPY --from=ext /tmp/grpc.so /opt/bref-extra/grpc.so
COPY --from=ext /tmp/ext.ini /opt/bref/etc/php/conf.d/ext-grpc.ini
