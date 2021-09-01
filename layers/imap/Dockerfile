ARG PHP_VERSION
FROM bref/build-php-$PHP_VERSION:1.2.13 AS ext

RUN LD_LIBRARY_PATH=/lib:/lib64 yum -y install amazon-linux-extras
RUN LD_LIBRARY_PATH=/lib:/lib64 amazon-linux-extras install epel -y
RUN LD_LIBRARY_PATH=/lib:/lib64 yum -y install uw-imap-devel krb5-devel openssl-devel libc-client-devel

WORKDIR ${PHP_BUILD_DIR}/ext/imap
RUN phpize
RUN ./configure --with-imap --with-kerberos --with-imap-ssl --with-libdir=lib64 --disable-static
RUN make -j `nproc` && make install

RUN cp `php-config --extension-dir`/imap.so /tmp/imap.so
RUN echo 'extension=/opt/bref-extra/imap.so' > /tmp/ext.ini

# Build the final image with just the files we need
FROM scratch

# Copy things we installed to the final image
COPY --from=ext /usr/lib64/libc-client.so.2007 /opt/bref/lib64/libc-client.so.2007
COPY --from=ext /usr/lib64/libpam.so.0 /opt/bref/lib64/libpam.so.0
COPY --from=ext /usr/lib64/libaudit.so.1 /opt/bref/lib64/libaudit.so.1
COPY --from=ext /usr/lib64/libcap-ng.so.0 /opt/bref/lib64/libcap-ng.so.0
COPY --from=ext /tmp/imap.so /opt/bref-extra/imap.so
COPY --from=ext /tmp/ext.ini /opt/bref/etc/php/conf.d/ext-imap.ini
