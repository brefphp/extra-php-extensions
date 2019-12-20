ARG PHP_VERSION
FROM bref/build-php-$PHP_VERSION AS ext

RUN LD_LIBRARY_PATH= yum install -y  php-pecl-memcached

# Build the final image from the lambci image that is close to the production environment
FROM lambci/lambda:provided

# Copy things we installed to the final image
COPY --from=ext /usr/lib64/php/modules/memcached.so /opt/bref/lib/php/extensions/no-debug-zts-20180731/memcached.so
