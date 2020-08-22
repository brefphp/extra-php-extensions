ARG PHP_VERSION
FROM bref/build-php-$PHP_VERSION AS ext

RUN pecl install mongodb
RUN cp `php-config --extension-dir`/mongodb.so /tmp/mongodb.so

# Build the final image from the lambci image that is close to the production environment
FROM lambci/lambda:provided

# Copy things we installed to the final image
COPY --from=ext /tmp/mongodb.so /opt/bref-extra/mongodb.so
