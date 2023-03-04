ARG PHP_VERSION
ARG BREF_VERSION
FROM bref/build-php-$PHP_VERSION:$BREF_VERSION AS ext

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
RUN ./configure \
    --disable-static \
    --enable-gd-jis-conv \
    --enable-shared \
    ${EXT_CONFIGURE_OPTS} \
    ;
RUN make -j $(nproc)
RUN make install
RUN cp "$(php-config --extension-dir)/gd.so" /tmp/gd.so
RUN echo 'extension=gd.so' > /tmp/ext.ini

RUN php /bref/lib-copy/copy-dependencies.php /tmp/gd.so /tmp/extension-libs


FROM scratch

COPY --from=ext /tmp/gd.so /opt/bref/extensions/gd.so
COPY --from=ext /tmp/ext.ini /opt/bref/etc/php/conf.d/ext-gd.ini
COPY --from=ext /tmp/extension-libs /opt/lib
