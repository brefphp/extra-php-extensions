ARG PHP_VERSION
FROM bref/build-php-$PHP_VERSION:latest AS ext

ENV LD_LIBRARY_PATH=/usr/lib:/usr/lib64:$LD_LIBRARY_PATH
RUN yum install -y re2c

RUN pecl install mailparse
RUN cp `php-config --extension-dir`/mailparse.so /tmp/mailparse.so

# Build the final image from the lambci image that is close to the production environment
FROM scratch

# Copy things we installed to the final image
COPY --from=ext /tmp/mailparse.so /opt/bref-extra/mailparse.so
