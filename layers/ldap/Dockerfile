ARG PHP_VERSION
FROM bref/build-php-$PHP_VERSION:1.1.4 AS ext

ENV LD_LIBRARY_PATH=/usr/lib:/usr/lib64:$LD_LIBRARY_PATH

RUN yum -y install openldap-devel

WORKDIR ${PHP_BUILD_DIR}/ext/ldap
RUN phpize
RUN ls /usr/include/ldap*
RUN ./configure --with-ldap --with-libdir=lib64
RUN make -j `nproc` && make install

RUN cp `php-config --extension-dir`/ldap.so /tmp/ldap.so
RUN echo 'extension=/opt/bref-extra/ldap.so' > /tmp/ext.ini

# Build the final image with just the files we need
FROM scratch

# Copy things we installed to the final image
COPY --from=ext /tmp/ldap.so /opt/bref-extra/ldap.so
COPY --from=ext /tmp/ext.ini /opt/bref/etc/php/conf.d/ext-ldap.ini
