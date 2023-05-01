ARG PHP_VERSION
ARG BREF_VERSION
FROM bref/build-php-$PHP_VERSION:$BREF_VERSION AS ext

RUN set -ex && \
    LD_LIBRARY_PATH=/lib64:/lib yum install -y libcurl-devel openssl-devel && \
    mkdir ${PHP_BUILD_DIR}/ext/swoole && \
    curl -sfL https://github.com/swoole/swoole-src/archive/v5.0.2.tar.gz -o swoole.tar.gz && \
    tar xfz swoole.tar.gz --strip-components=1 -C ${PHP_BUILD_DIR}/ext/swoole && \
    cd  ${PHP_BUILD_DIR}/ext/swoole && \
    phpize && \
    ./configure --enable-openssl --enable-sockets --enable-swoole-curl && \
    make -j $(nproc) && \
    make install && \
    cp "$(php-config --extension-dir)/swoole.so" /tmp/swoole.so && \
    echo 'extension=swoole.so' > /tmp/ext.ini

# Build the final image with just the files we need
FROM scratch

# Copy things we installed to the final image
COPY --from=ext /tmp/swoole.so /opt/bref/extensions/swoole.so
COPY --from=ext /tmp/ext.ini /opt/bref/etc/php/conf.d/ext-swoole.ini
