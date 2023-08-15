ARG PHP_VERSION
ARG BREF_VERSION
FROM bref/build-php-$PHP_VERSION:$BREF_VERSION AS ext
ARG PHP_VERSION

# Prepare environment
ENV IMAGICK_BUILD_DIR=${BUILD_DIR}/imagick
RUN mkdir -p ${IMAGICK_BUILD_DIR}
RUN LD_LIBRARY_PATH= yum -y install libpng-devel libjpeg-devel lcms2-devel ImageMagick-devel

# Compile libwebp since AL2 ships with v0.3, and v0.4 or higher is required to builder the other libs
WORKDIR ${IMAGICK_BUILD_DIR}
RUN curl -Ls -o libwebp.tar.gz https://github.com/webmproject/libwebp/archive/refs/tags/v1.3.1.tar.gz
RUN tar xzf libwebp.tar.gz
WORKDIR ${IMAGICK_BUILD_DIR}/libwebp-1.3.1
RUN autoreconf -i && automake && autoconf
RUN ./configure --prefix ${INSTALL_DIR} --exec-prefix ${INSTALL_DIR}
RUN make -j $(nproc)
RUN make install

# Compile libde265 (libheif dependency)
WORKDIR ${IMAGICK_BUILD_DIR}
RUN curl -Ls -o libde265.tar.gz https://github.com/strukturag/libde265/releases/download/v1.0.12/libde265-1.0.12.tar.gz
RUN tar xzf libde265.tar.gz
WORKDIR ${IMAGICK_BUILD_DIR}/libde265-1.0.12
RUN ./configure --prefix ${INSTALL_DIR} --exec-prefix ${INSTALL_DIR}
RUN make -j $(nproc)
RUN make install

# Compile libheif
WORKDIR ${IMAGICK_BUILD_DIR}
RUN curl -Ls -o libheif.tar.gz https://github.com/strukturag/libheif/releases/download/v1.13.0/libheif-1.13.0.tar.gz
RUN tar xzf libheif.tar.gz
WORKDIR ${IMAGICK_BUILD_DIR}/libheif-1.13.0
RUN ./configure --prefix ${INSTALL_DIR} --exec-prefix ${INSTALL_DIR}
RUN make -j $(nproc)
RUN make install

# Compile gs
WORKDIR ${IMAGICK_BUILD_DIR}
RUN curl -Ls -o ghostscript.tar.gz https://github.com/ArtifexSoftware/ghostpdl-downloads/releases/download/gs9561/ghostscript-9.56.1.tar.gz
RUN tar xzf ghostscript.tar.gz
WORKDIR ${IMAGICK_BUILD_DIR}/ghostscript-9.56.1
RUN ./configure --prefix ${INSTALL_DIR} --exec-prefix ${INSTALL_DIR} --without-x
RUN make -j $(nproc)
RUN cp bin/gs /tmp/gs

# Compile the ImageMagick library
WORKDIR ${IMAGICK_BUILD_DIR}
RUN curl -Ls -o ImageMagick.tar.gz https://github.com/ImageMagick/ImageMagick/archive/refs/tags/7.1.1-15.tar.gz
RUN tar xzf ImageMagick.tar.gz
WORKDIR ${IMAGICK_BUILD_DIR}/ImageMagick-7.1.1-15
RUN ./configure --prefix ${INSTALL_DIR} --exec-prefix ${INSTALL_DIR} --with-webp --with-heic --disable-static --with-freetype=yes
RUN make -j $(nproc)
RUN make install

# Show how ImageMagick is configured. See the "delicate" section
RUN convert -list configure

# Compile the php imagick extension
WORKDIR ${IMAGICK_BUILD_DIR}
RUN git clone https://github.com/Imagick/imagick
WORKDIR ${IMAGICK_BUILD_DIR}/imagick
RUN git reset --hard 28f27044e435a2b203e32675e942eb8de620ee58
RUN phpize
RUN ./configure --with-imagick=${INSTALL_DIR}
RUN make -j $(nproc)
RUN make install

RUN cp `php-config --extension-dir`/imagick.so /tmp/imagick.so
RUN strip --strip-debug /tmp/imagick.so
RUN echo 'extension=imagick.so' > /tmp/ext.ini

RUN php /bref/lib-copy/copy-dependencies.php /tmp/imagick.so /tmp/extension-libs

# Build the final image with just the files we need
FROM scratch

# Copy things we installed to the final image
COPY --from=ext /tmp/gs /opt/bin/gs
COPY --from=ext /tmp/imagick.so /opt/bref/extensions/imagick.so
COPY --from=ext /tmp/ext.ini /opt/bref/etc/php/conf.d/ext-imagick.ini
COPY --from=ext /tmp/extension-libs /opt/lib
