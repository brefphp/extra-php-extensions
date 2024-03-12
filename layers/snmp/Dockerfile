ARG PHP_VERSION
ARG BREF_VERSION
FROM bref/build-php-$PHP_VERSION:$BREF_VERSION AS ext

RUN LD_LIBRARY_PATH=/lib:/lib64 yum -y install amazon-linux-extras
RUN LD_LIBRARY_PATH=/lib:/lib64 amazon-linux-extras install epel -y
RUN LD_LIBRARY_PATH=/lib:/lib64 yum -y update
RUN LD_LIBRARY_PATH=/lib:/lib64 yum -y install net-snmp net-snmp-{devel,utils}

WORKDIR ${PHP_BUILD_DIR}/ext/snmp
RUN phpize
RUN ./configure --with-libdir=lib64 --disable-static --with-sn
RUN make -j `nproc` && make install

RUN cp `php-config --extension-dir`/snmp.so /tmp/snmp.so
RUN echo 'extension=snmp.so' > /tmp/ext.ini

RUN php /bref/lib-copy/copy-dependencies.php /tmp/snmp.so /tmp/extension-libs

# Build the final image with just the files we need
FROM scratch

# Copy things we installed to the final image
COPY --from=ext /tmp/snmp.so /opt/bref/extensions/snmp.so
COPY --from=ext /tmp/ext.ini /opt/bref/etc/php/conf.d/ext-snmp.ini
COPY --from=ext /tmp/extension-libs /opt/lib
