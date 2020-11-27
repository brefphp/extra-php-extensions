ARG PHP_VERSION
FROM bref/build-php-$PHP_VERSION:1.0.0 AS ext

# Prepare environment
ENV IMAGICK_BUILD_DIR=${BUILD_DIR}/imagick
RUN mkdir -p ${IMAGICK_BUILD_DIR}
RUN LD_LIBRARY_PATH= yum -y install libwebp-devel wget libpng-devel libjpeg-devel

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

# Compile the ImageMagick library
WORKDIR ${IMAGICK_BUILD_DIR}
RUN wget https://github.com/ImageMagick/ImageMagick/archive/7.0.10-43.tar.gz -O ImageMagick.tar.gz
RUN tar xzf ImageMagick.tar.gz
WORKDIR ${IMAGICK_BUILD_DIR}/ImageMagick-7.0.10-43
RUN ./configure --prefix ${INSTALL_DIR} --exec-prefix ${INSTALL_DIR} --with-webp --with-heic --disable-static
RUN make -j $(nproc)
RUN make install

# Compile the php imagick extension
WORKDIR ${IMAGICK_BUILD_DIR}
RUN pecl download imagick
RUN tar xzf imagick-3.4.4.tgz
WORKDIR ${IMAGICK_BUILD_DIR}/imagick-3.4.4
RUN phpize
RUN ./configure --with-imagick=${INSTALL_DIR}
RUN make -j $(nproc)
RUN make install
RUN cp `php-config --extension-dir`/imagick.so /tmp/imagick.so
RUN echo 'extension=/opt/bref-extra/imagick.so' > /tmp/ext.ini

FROM scratch

# The ImageMagick libraries needed by the extension
COPY --from=ext /opt/bref/lib/libMagickWand-7.Q16HDRI.so.8.0.0 /opt/bref/lib/libMagickWand-7.Q16HDRI.so.8
COPY --from=ext /opt/bref/lib/libMagickCore-7.Q16HDRI.so.8.0.0 /opt/bref/lib/libMagickCore-7.Q16HDRI.so.8

# ImageMagick dependencies
COPY --from=ext /usr/lib64/libpng15.so.15.13.0 /opt/bref/lib/libpng15.so.15
COPY --from=ext /usr/lib64/libjpeg.so.62.1.0 /opt/bref/lib/libjpeg.so.62
COPY --from=ext /usr/lib64/libwebp.so.4.0.2 /opt/bref/lib/libwebp.so.4
COPY --from=ext /usr/lib64/libgomp.so.1.0.0 /opt/bref/lib/libgomp.so.1
COPY --from=ext /opt/bref/lib/libde265.so.0.0.12 /opt/bref/lib/libde265.so.0
COPY --from=ext /opt/bref/lib/libheif.so.1.6.2 /opt/bref/lib/libheif.so.1

COPY --from=ext /tmp/imagick.so /opt/bref-extra/imagick.so
COPY --from=ext /tmp/ext.ini /opt/bref/etc/php/conf.d/ext-imagick.ini
