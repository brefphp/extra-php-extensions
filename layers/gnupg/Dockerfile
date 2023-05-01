ARG PHP_VERSION
ARG BREF_VERSION
FROM bref/build-php-$PHP_VERSION:$BREF_VERSION AS ext

ENV LD_LIBRARY_PATH=/usr/lib:/usr/lib64:$LD_LIBRARY_PATH
RUN yum install -y gpgme-devel

RUN pecl install gnupg
RUN cp `php-config --extension-dir`/gnupg.so /tmp/gnupg.so
RUN echo 'extension=gnupg.so' > /tmp/ext.ini

RUN php /bref/lib-copy/copy-dependencies.php /tmp/gnupg.so /tmp/extension-libs


FROM scratch

COPY --from=ext /tmp/gnupg.so /opt/bref/extensions/gnupg.so
COPY --from=ext /tmp/ext.ini /opt/bref/etc/php/conf.d/ext-gnupg.ini
COPY --from=ext /tmp/extension-libs /opt/lib
