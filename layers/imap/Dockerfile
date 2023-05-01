ARG PHP_VERSION
ARG BREF_VERSION
FROM bref/build-php-$PHP_VERSION:$BREF_VERSION AS ext

RUN LD_LIBRARY_PATH=/lib:/lib64 yum -y install amazon-linux-extras
RUN LD_LIBRARY_PATH=/lib:/lib64 amazon-linux-extras install epel -y
RUN LD_LIBRARY_PATH=/lib:/lib64 yum -y install uw-imap-devel krb5-devel openssl-devel libc-client-devel

WORKDIR ${PHP_BUILD_DIR}/ext/imap
RUN phpize
RUN ./configure --with-imap --with-kerberos --with-imap-ssl --with-libdir=lib64 --disable-static
RUN make -j `nproc` && make install

RUN cp `php-config --extension-dir`/imap.so /tmp/imap.so
RUN echo 'extension=imap.so' > /tmp/ext.ini

RUN php /bref/lib-copy/copy-dependencies.php /tmp/imap.so /tmp/extension-libs


# Build the final image with just the files we need
FROM scratch

# Copy things we installed to the final image
COPY --from=ext /tmp/imap.so /opt/bref/extensions/imap.so
COPY --from=ext /tmp/ext.ini /opt/bref/etc/php/conf.d/ext-imap.ini
COPY --from=ext /tmp/extension-libs /opt/lib
