ARG PHP_VERSION
FROM bref/build-php-$PHP_VERSION:1.7.14 AS ext
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
RUN echo 'extension=/opt/bref-extra/maxminddb.so' > /tmp/ext.ini

# Build the final image with just the files we need
FROM scratch

# Copy things we installed to the final image
COPY --from=ext /opt/bref/include/maxminddb.h /opt/bref/include/maxminddb.h
COPY --from=ext /opt/bref/include/maxminddb_config.h /opt/bref/include/maxminddb_config.h

COPY --from=ext /opt/bref/lib/libmaxminddb.* /opt/bref/lib/
COPY --from=ext /opt/bref/lib/pkgconfig/libmaxminddb.pc /opt/bref/lib/pkgconfig/libmaxminddb.pc

COPY --from=ext /tmp/maxminddb.so /opt/bref-extra/maxminddb.so
COPY --from=ext /tmp/ext.ini /opt/bref/etc/php/conf.d/ext-maxminddb.ini
