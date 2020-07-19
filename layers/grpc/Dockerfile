ARG PHP_VERSION
FROM bref/build-php-$PHP_VERSION as ext

RUN pecl install grpc > /dev/null
RUN cp `php-config --extension-dir`/grpc.so /tmp/grpc.so

# Build the final image from the lambci image that is close to the production environment
FROM lambci/lambda:provided

# Copy things we installed to the final image
COPY --from=ext /tmp/grpc.so /opt/bref-extra/grpc.so
