ARG PHP_VERSION
FROM bref/build-php-$PHP_VERSION:1.7.14 AS ext

ENV LD_LIBRARY_PATH=/usr/lib:/usr/lib64:$LD_LIBRARY_PATH

RUN yum install -y gpgme-devel

RUN pecl install gnupg
RUN cp `php-config --extension-dir`/gnupg.so /tmp/gnupg.so
RUN echo 'extension=/opt/bref-extra/gnupg.so' > /tmp/ext.ini

# Build the final image with just the files we need
FROM scratch

# Copy things we installed to the final image

COPY --from=ext /tmp/gnupg.so /opt/bref-extra/gnupg.so
COPY --from=ext /tmp/ext.ini /opt/bref/etc/php/conf.d/ext-gnupg.ini
