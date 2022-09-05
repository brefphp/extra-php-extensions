ARG PHP_VERSION
FROM bref/build-php-$PHP_VERSION:1.7.4 AS ext

ENV LIBRDKAFKA_BUILD_DIR=${BUILD_DIR}/librdkafka

RUN set -xe; \
    mkdir -p ${LIBRDKAFKA_BUILD_DIR}; \
    # Download and upack the source code
    curl -Ls  https://github.com/edenhill/librdkafka/archive/refs/tags/v1.8.2.tar.gz   \
    | tar xzC ${LIBRDKAFKA_BUILD_DIR}

# Move into the unpackaged code directory
WORKDIR  ${LIBRDKAFKA_BUILD_DIR}/librdkafka-1.8.2/

# Install
RUN set -xe; \
    ./configure --prefix=${INSTALL_DIR} && \
    make && \
    make install

RUN pecl install --onlyreqdeps --nobuild rdkafka && \
    cd "$(pecl config-get temp_dir)/rdkafka" && \
    phpize && \
    ./configure --with-rdkafka=${INSTALL_DIR} && \
    make && \
    make install  && \
    cd -

RUN cp /opt/bref/lib/php/extensions/no-debug-non-zts-*/rdkafka.so /tmp/rdkafka.so
RUN echo 'extension=/opt/bref-extra/rdkafka.so' > /tmp/ext.ini


# Build the final image with just the files we need
FROM scratch

# Copy things we installed to the final image
COPY --from=ext /tmp/rdkafka.so /opt/bref-extra/rdkafka.so
COPY --from=ext /tmp/ext.ini /opt/bref/etc/php/conf.d/ext-rdkafka.ini
COPY --from=ext /opt/bref/lib/librdkafka* /opt/bref/lib/
COPY --from=ext /opt/bref/include/librdkafka /opt/bref/include/
