ARG PHP_VERSION
FROM bref/build-php-$PHP_VERSION:1.7.14 AS ext

ENV LD_LIBRARY_PATH=/usr/lib:/usr/lib64:$LD_LIBRARY_PATH
RUN yum install -y gpgme-devel

RUN pecl install gnupg
RUN cp `php-config --extension-dir`/gnupg.so /tmp/gnupg.so
RUN echo 'extension=/opt/bref-extra/gnupg.so' > /tmp/ext.ini

FROM scratch

COPY --from=ext /usr/lib64/libgpgme.so.11.8.1 /opt/bref/lib64/libgpgme.so.11
COPY --from=ext /usr/lib64/libassuan.so.0.4.0 /opt/bref/lib64/libassuan.so.0
COPY --from=ext /usr/lib64/libgcrypt.so.11.8.2 /opt/bref/lib64/libgcrypt.so.11

COPY --from=ext /tmp/gnupg.so /opt/bref-extra/gnupg.so
COPY --from=ext /tmp/ext.ini /opt/bref/etc/php/conf.d/ext-gnupg.ini
