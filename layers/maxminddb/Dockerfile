ARG PHP_VERSION
ARG BREF_VERSION
FROM bref/build-php-$PHP_VERSION:$BREF_VERSION AS ext
ARG PHP_VERSION

# Install libmaxminddb
ENV LIBMAXMINDDB_BUILD_DIR=${BUILD_DIR}/libmaxminddb
RUN mkdir -p ${LIBMAXMINDDB_BUILD_DIR}

RUN curl -Ls  https://github.com/maxmind/libmaxminddb/releases/download/1.6.0/libmaxminddb-1.6.0.tar.gz  \
    | tar xzC ${LIBMAXMINDDB_BUILD_DIR}

WORKDIR ${LIBMAXMINDDB_BUILD_DIR}/libmaxminddb-1.6.0

RUN ./configure --prefix ${INSTALL_DIR} --exec-prefix ${INSTALL_DIR}
RUN make -j `nproc`
RUN make install

# Install maxminddb
RUN pecl install maxminddb
RUN cp `php-config --extension-dir`/maxminddb.so /tmp/maxminddb.so
RUN echo 'extension=maxminddb.so' > /tmp/ext.ini

RUN php /bref/lib-copy/copy-dependencies.php /tmp/maxminddb.so /tmp/extension-libs


# Build the final image with just the files we need
FROM scratch

# Copy things we installed to the final image
COPY --from=ext /tmp/maxminddb.so /opt/bref/extensions/maxminddb.so
COPY --from=ext /tmp/ext.ini /opt/bref/etc/php/conf.d/ext-maxminddb.ini
COPY --from=ext /tmp/extension-libs /opt/lib
