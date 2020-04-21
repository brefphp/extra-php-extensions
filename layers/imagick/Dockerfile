ARG PHP_VERSION
FROM bref/build-php-$PHP_VERSION as ext

# Prepare environment
ENV IMAGICK_BUILD_DIR=${BUILD_DIR}/imagick
RUN mkdir -p ${IMAGICK_BUILD_DIR}
WORKDIR ${IMAGICK_BUILD_DIR}
RUN LD_LIBRARY_PATH= yum -y install libwebp-devel

# Compile the ImageMagick library
RUN curl https://imagemagick.org/download/ImageMagick-6.9.11-7.tar.gz > ImageMagick.tar.gz
RUN tar xzf ImageMagick.tar.gz
WORKDIR ${IMAGICK_BUILD_DIR}/ImageMagick-6.9.11-7
RUN ./configure --prefix ${INSTALL_DIR} --exec-prefix ${INSTALL_DIR} --with-webp
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

FROM lambci/lambda:provided

# The libraries needed by the extension
COPY --from=ext /opt/bref/lib/libMagickWand-6.Q16.so.6.0.0 /opt/bref/lib/libMagickWand-6.Q16.so.6
COPY --from=ext /opt/bref/lib/libMagickCore-6.Q16.so.6.0.0 /opt/bref/lib/libMagickCore-6.Q16.so.6
COPY --from=ext /usr/lib64/libwebp.so.4.0.2 /opt/bref/lib/libwebp.so.4

COPY --from=ext /tmp/imagick.so /opt/bref-extra/imagick.so
