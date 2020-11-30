ARG PHP_VERSION
FROM bref/build-php-$PHP_VERSION:1.0.0 AS ext

WORKDIR ${PHP_BUILD_DIR}/ext/pgsql
RUN phpize
RUN ./configure --with-pgsql=${INSTALL_DIR}
RUN make -j `nproc` && make install

RUN cp `php-config --extension-dir`/pgsql.so /tmp/pgsql.so
RUN echo 'extension=/opt/bref-extra/pgsql.so' > /tmp/ext.ini

# Build the final image from the lambci image that is close to the production environment
FROM scratch

# Copy things we installed to the final image
COPY --from=ext /tmp/pgsql.so /opt/bref-extra/pgsql.so
COPY --from=ext /tmp/ext.ini /opt/bref/etc/php/conf.d/ext-pgsql.ini
