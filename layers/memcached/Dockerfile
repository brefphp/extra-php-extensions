ARG PHP_VERSION
FROM bref/build-php-$PHP_VERSION as ext

ENV LIBMEMCACHED_BUILD_DIR=${BUILD_DIR}/libmemcached

RUN set -xe; \
    mkdir -p ${LIBMEMCACHED_BUILD_DIR}; \
# Download and upack the source code
    curl -Ls  https://launchpad.net/libmemcached/1.0/1.0.18/+download/libmemcached-1.0.18.tar.gz  \
  | tar xzC ${LIBMEMCACHED_BUILD_DIR}

RUN curl -Ls -o ${LIBMEMCACHED_BUILD_DIR}/libmemcached-1.0.18/libmemcached-build.patch https://src.fedoraproject.org/rpms/libmemcached/raw/HEAD/f/libmemcached-build.patch

# Move into the unpackaged code directory
WORKDIR  ${LIBMEMCACHED_BUILD_DIR}/libmemcached-1.0.18/

# Install
RUN set -xe; \
    patch -d . -p0 < libmemcached-build.patch && \
    ./configure --prefix=${INSTALL_DIR} && \
    make && \
    make install

RUN pecl install memcache
RUN cp /opt/bref/lib/php/extensions/no-debug-zts-*/memcache.so /tmp/memcache.so

# Build the final image from the lambci image that is close to the production environment
FROM lambci/lambda:provided

# Copy things we installed to the final image
COPY --from=ext /opt/bref/include/libmemcached/ /opt/bref/include/libmemcached/
COPY --from=ext /opt/bref/include/libmemcached-1.0/ /opt/bref/include/libmemcached-1.0/
COPY --from=ext /opt/bref/include/libmemcachedutil-1.0/ /opt/bref/include/libmemcachedutil-1.0/
COPY --from=ext /opt/bref/include/libhashkit/ /opt/bref/include/libhashkit/
COPY --from=ext /opt/bref/include/libhashkit-1.0/ /opt/bref/include/libhashkit-1.0/

COPY --from=ext /opt/bref/lib/libhashkit.* /opt/bref/lib/
COPY --from=ext /opt/bref/lib/libmemcached.* /opt/bref/lib/
COPY --from=ext /opt/bref/lib/libmemcachedutil.* /opt/bref/lib/
COPY --from=ext /opt/bref/lib/pkgconfig/libmemcached.pc /opt/bref/lib/pkgconfig/libmemcached.pc
#
COPY --from=ext /tmp/memcache.so /opt/bref-extra/memcache.so
