ARG PHP_VERSION
FROM bref/build-php-$PHP_VERSION:1.0.0-beta1 AS ext

# Install required build libraries
RUN set -ue \
    ; LD_LIBRARY_PATH= \
    ; yum -y install \
        libwebp-devel \
        libXpm-devel \
        libpng-devel \
        libjpeg-devel \
        freetype-devel \
;

WORKDIR ${PHP_BUILD_DIR}/ext/gd
RUN phpize
ARG PHP_VERSION
ENV EXT_CONFIGURE_OPTS=" \
    --with-freetype \
    --enable-gd \
    --with-jpeg \
    --with-png \
    --with-webp \
    --with-xpm \
    --with-zlib \
"
RUN if [ "$PHP_VERSION" = "73" ] ; \
    then \
        EXT_CONFIGURE_OPTS=" \
        --with-freetype-dir=${INSTALL_DIR} \
        --with-gd \
        --with-jpeg-dir=${INSTALL_DIR} \
        --with-png-dir=${INSTALL_DIR} \
        --with-webp-dir=${INSTALL_DIR} \
        --with-xpm-dir=${INSTALL_DIR} \
        --with-zlib-dir=${INSTALL_DIR} \
        " ; \
    fi; \
    ./configure \
        --disable-static \
        --enable-gd-jis-conv \
        --enable-shared \
        ${EXT_CONFIGURE_OPTS} \
;
RUN make -j $(nproc)
RUN make install
RUN cp "$(php-config --extension-dir)/gd.so" /tmp/gd.so

FROM scratch

COPY --from=ext /usr/lib64/libXpm.so.4 /opt/bref/lib64/libXpm.so.4
COPY --from=ext /usr/lib64/libwebp.so.4 /opt/bref/lib64/libwebp.so.4

COPY --from=ext /tmp/gd.so /opt/bref-extra/gd.so
