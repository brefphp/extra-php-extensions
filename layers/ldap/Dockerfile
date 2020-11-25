ARG PHP_VERSION
FROM bref/build-php-$PHP_VERSION:1.0.0 AS ext

ENV LD_LIBRARY_PATH=/usr/lib:/usr/lib64:$LD_LIBRARY_PATH

RUN yum -y install openldap-devel

WORKDIR ${PHP_BUILD_DIR}/ext/ldap
RUN phpize
RUN ls /usr/include/ldap*
RUN ./configure --with-ldap --with-libdir=lib64
RUN make -j `nproc` && make install

RUN cp `php-config --extension-dir`/ldap.so /tmp/ldap.so

# Build the final image from the lambci image that is close to the production environment
FROM scratch

# Copy things we installed to the final image
COPY --from=ext /tmp/ldap.so /opt/bref-extra/ldap.so
