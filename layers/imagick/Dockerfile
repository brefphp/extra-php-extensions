ARG PHP_VERSION
ARG BREF_VERSION
FROM bref/build-php-$PHP_VERSION:$BREF_VERSION AS ext
ARG PHP_VERSION

# Prepare environment
ENV IMAGICK_BUILD_DIR=${BUILD_DIR}/imagick
RUN mkdir -p ${IMAGICK_BUILD_DIR}
RUN LD_LIBRARY_PATH= yum -y install libwebp-devel wget libpng-devel libjpeg-devel lcms2-devel ImageMagick-devel

# Compile libde265 (libheif dependency)
WORKDIR ${IMAGICK_BUILD_DIR}
RUN wget https://github.com/strukturag/libde265/releases/download/v1.0.5/libde265-1.0.5.tar.gz -O libde265.tar.gz
RUN tar xzf libde265.tar.gz
WORKDIR ${IMAGICK_BUILD_DIR}/libde265-1.0.5
RUN ./configure --prefix ${INSTALL_DIR} --exec-prefix ${INSTALL_DIR}
RUN make -j $(nproc)
RUN make install

# Compile libheif
WORKDIR ${IMAGICK_BUILD_DIR}
RUN wget https://github.com/strukturag/libheif/releases/download/v1.6.2/libheif-1.6.2.tar.gz -O libheif.tar.gz
RUN tar xzf libheif.tar.gz
WORKDIR ${IMAGICK_BUILD_DIR}/libheif-1.6.2
RUN ./configure --prefix ${INSTALL_DIR} --exec-prefix ${INSTALL_DIR}
RUN make -j $(nproc)
RUN make install

# Compile gs
WORKDIR ${IMAGICK_BUILD_DIR}
RUN wget https://github.com/ArtifexSoftware/ghostpdl-downloads/releases/download/gs9533/ghostscript-9.53.3.tar.gz -O ghostscript.tar.gz
RUN tar xzf ghostscript.tar.gz
WORKDIR ${IMAGICK_BUILD_DIR}/ghostscript-9.53.3
RUN ./configure --prefix ${INSTALL_DIR} --exec-prefix ${INSTALL_DIR} --without-x
RUN make -j $(nproc)
RUN cp bin/gs /tmp/gs

# Compile the ImageMagick library
WORKDIR ${IMAGICK_BUILD_DIR}
RUN wget https://github.com/ImageMagick/ImageMagick/archive/7.0.10-43.tar.gz -O ImageMagick.tar.gz
RUN tar xzf ImageMagick.tar.gz
WORKDIR ${IMAGICK_BUILD_DIR}/ImageMagick-7.0.10-43
RUN ./configure --prefix ${INSTALL_DIR} --exec-prefix ${INSTALL_DIR} --with-webp --with-heic --disable-static --with-freetype=yes
RUN make -j $(nproc)
RUN make install

# Show how ImageMagick is configured. See the "delicate" section
RUN convert -list configure

# Compile the php imagick extension
WORKDIR ${IMAGICK_BUILD_DIR}
# Temporary workaround for getting right imagick both for PHP 7.* and 8.*
# @see https://github.com/brefphp/extra-php-extensions/pull/143/files#r541977286
RUN git clone https://github.com/Imagick/imagick
WORKDIR ${IMAGICK_BUILD_DIR}/imagick
RUN git checkout 3.7.0
RUN phpize
RUN ./configure --with-imagick=${INSTALL_DIR}
RUN make -j $(nproc)
RUN make install

RUN cp `php-config --extension-dir`/imagick.so /tmp/imagick.so
RUN echo 'extension=imagick.so' > /tmp/ext.ini

RUN php /bref/lib-copy/copy-dependencies.php /tmp/imagick.so /tmp/extension-libs


FROM scratch

COPY --from=ext /tmp/gs /opt/bin/gs
COPY --from=ext /tmp/imagick.so /opt/bref/extensions/imagick.so
COPY --from=ext /tmp/ext.ini /opt/bref/etc/php/conf.d/ext-imagick.ini
COPY --from=ext /tmp/extension-libs /opt/lib
