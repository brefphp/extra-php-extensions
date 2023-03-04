ARG PHP_VERSION
ARG BREF_VERSION
FROM bref/build-php-$PHP_VERSION:$BREF_VERSION AS ext

ENV LIBMEMCACHED_BUILD_DIR=${BUILD_DIR}/libmemcached

RUN set -xe; \
    mkdir -p ${LIBMEMCACHED_BUILD_DIR}; \
    # Download and upack the source code
    curl -Ls  https://launchpad.net/libmemcached/1.0/1.0.18/+download/libmemcached-1.0.18.tar.gz  \
    | tar xzC ${LIBMEMCACHED_BUILD_DIR}

# patch from https://src.fedoraproject.org/rpms/libmemcached/raw/HEAD/f/libmemcached-build.patch for build with GCC7
COPY libmemcached-build.patch ${LIBMEMCACHED_BUILD_DIR}/libmemcached-1.0.18/libmemcached-build.patch

# Move into the unpackaged code directory
WORKDIR  ${LIBMEMCACHED_BUILD_DIR}/libmemcached-1.0.18/

# Install
RUN set -xe; \
    patch -d . -p0 < libmemcached-build.patch && \
    ./configure --prefix=${INSTALL_DIR} && \
    make -j `nproc` && \
    make install

RUN pecl install --onlyreqdeps --nobuild memcached && \
    cd "$(pecl config-get temp_dir)/memcached" && \
    phpize && \
    ./configure --with-libmemcached-dir=${INSTALL_DIR}  --disable-memcached-sasl && \
    make -j `nproc` && \
    make install  && \
    cd -

RUN cp `php-config --extension-dir`/memcached.so /tmp/memcached.so
RUN echo 'extension=memcached.so' > /tmp/ext.ini

RUN php /bref/lib-copy/copy-dependencies.php /tmp/memcached.so /tmp/extension-libs


# Build the final image with just the files we need
FROM scratch

# Copy things we installed to the final image
COPY --from=ext /tmp/memcached.so /opt/bref/extensions/memcached.so
COPY --from=ext /tmp/ext.ini /opt/bref/etc/php/conf.d/ext-memcached.ini
COPY --from=ext /tmp/extension-libs /opt/lib
