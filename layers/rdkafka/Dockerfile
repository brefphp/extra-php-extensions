ARG PHP_VERSION
ARG BREF_VERSION
FROM bref/build-php-$PHP_VERSION:$BREF_VERSION AS ext

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
    make -j `nproc` && \
    make install  && \
    cd -

RUN cp `php-config --extension-dir`/rdkafka.so /tmp/rdkafka.so
RUN echo 'extension=rdkafka.so' > /tmp/ext.ini

RUN php /bref/lib-copy/copy-dependencies.php /tmp/rdkafka.so /tmp/extension-libs


# Build the final image with just the files we need
FROM scratch

# Copy things we installed to the final image
COPY --from=ext /tmp/rdkafka.so /opt/bref/extensions/rdkafka.so
COPY --from=ext /tmp/ext.ini /opt/bref/etc/php/conf.d/ext-rdkafka.ini
COPY --from=ext /tmp/extension-libs /opt/lib
