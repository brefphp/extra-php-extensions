ARG PHP_VERSION
FROM bref/build-php-$PHP_VERSION as ext

# Install required build libraries
RUN set -ue \
    ; LD_LIBRARY_PATH= \
    ; yum -y install \
        libwebp-devel \
        libXpm-devel \
    ; yum clean all \
;

# Compile the GD extension
WORKDIR ${PHP_BUILD_DIR}/ext/gd
RUN phpize
RUN ./configure \
        --disable-static \
        --enable-gd \
        --enable-gd-jis-conv \
        --enable-shared \
        --with-freetype \
        --with-jpeg \
        --with-webp \
        --with-xpm \
;
RUN make -j $(nproc)
RUN make install


RUN cp "$(php-config --extension-dir)/gd.so" /tmp/gd.so


FROM lambci/lambda:provided

COPY --from=ext /usr/lib64/libpng12.so.0 /opt/bref/lib/libpng12.so.0
COPY --from=ext /usr/lib64/libwebp.so.4 /opt/bref/lib/libwebp.so.4
COPY --from=ext /usr/lib64/libXpm.so.4 /opt/bref/lib/libXpm.so.4
COPY --from=ext /usr/lib64/libX11.so.6 /opt/bref/lib/libX11.so.6
COPY --from=ext /usr/lib64/libfreetype.so.6 /opt/bref/lib/libfreetype.so.6
COPY --from=ext /tmp/gd.so /opt/bref-extra/gd.so
