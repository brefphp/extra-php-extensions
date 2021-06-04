ARG PHP_VERSION
FROM bref/build-php-$PHP_VERSION:1.1.4 AS ext

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
RUN echo 'extension=/opt/bref-extra/gd.so' > /tmp/ext.ini

FROM scratch

COPY --from=ext /usr/lib64/libXpm.so.4 /opt/bref/lib64/libXpm.so.4
COPY --from=ext /usr/lib64/libX11.so.6.3.0 /opt/bref/lib64/libX11.so.6
COPY --from=ext /usr/lib64/libpng15.so.15.13.0 /opt/bref/lib64/libpng15.so.15
COPY --from=ext /usr/lib64/libjpeg.so.* /opt/bref/lib64/
COPY --from=ext /usr/lib64/libfreetype.so.6.14.0 /opt/bref/lib64/libfreetype.so.6
COPY --from=ext /usr/lib64/libxcb.so.1.1.0 /opt/bref/lib64/libxcb.so.1
COPY --from=ext /usr/lib64/libXau.so.6.0.0 /opt/bref/lib64/libXau.so.6
COPY --from=ext /usr/lib64/libwebp.so.4 /opt/bref/lib64/libwebp.so.4

COPY --from=ext /tmp/gd.so /opt/bref-extra/gd.so
COPY --from=ext /tmp/ext.ini /opt/bref/etc/php/conf.d/ext-gd.ini
