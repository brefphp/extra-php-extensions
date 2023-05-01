ARG PHP_VERSION
ARG BREF_VERSION
FROM bref/build-php-$PHP_VERSION:$BREF_VERSION AS ext

RUN LD_LIBRARY_PATH=/lib64:/lib yum install -y libssh2 libssh2-devel
RUN pecl install ssh2-beta
RUN cp `php-config --extension-dir`/ssh2.so /tmp/ssh2.so
RUN echo 'extension=ssh2.so' > /tmp/ext.ini

# Build the final image with just the files we need
FROM scratch

# Copy things we installed to the final image
COPY --from=ext /tmp/ssh2.so /opt/bref/extensions/ssh2.so
COPY --from=ext /tmp/ext.ini /opt/bref/etc/php/conf.d/ext-ssh2.ini
