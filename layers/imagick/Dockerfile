ARG PHP_VERSION
ARG BREF_VERSION
FROM bref/build-php-$PHP_VERSION:$BREF_VERSION AS ext
ARG PHP_VERSION

ENV IMAGICK_VERSION="7.1.1-38"
ENV AOM_VERSION="3.10.0"
ENV LIBHEIF_VERSION="1.19.3"
ENV LIBDE265_VERSION="1.0.15"
ENV GS_VERSION="9.56.1"
ENV IMAGICK_EXT_COMMIT="28f27044e435a2b203e32675e942eb8de620ee58"

# Prepare environment
ENV IMAGICK_BUILD_DIR=${BUILD_DIR}/imagick
RUN mkdir -p ${IMAGICK_BUILD_DIR}
WORKDIR ${IMAGICK_BUILD_DIR}
RUN LD_LIBRARY_PATH= dnf -y install libpng-devel libjpeg-devel lcms2-devel libwebp-devel nasm cmake

# Compile AOM (libavif dependency for AVIF support)
RUN curl -Ls -o aom.tar.gz https://storage.googleapis.com/aom-releases/libaom-${AOM_VERSION}.tar.gz && tar xzf aom.tar.gz && rm aom.tar.gz \
    && mkdir -p ${IMAGICK_BUILD_DIR}/aom_build && cd ${IMAGICK_BUILD_DIR}/aom_build  \
    && cmake ../libaom-${AOM_VERSION} -DCMAKE_INSTALL_PREFIX=${INSTALL_DIR} -DBUILD_SHARED_LIBS=1 -DENABLE_DOCS=0 -DENABLE_EXAMPLES=0 -DENABLE_TESTDATA=0 -DENABLE_TESTS=0 -DENABLE_TOOLS=0 \
    && make -j $(nproc) && make install && rm -rf ${IMAGICK_BUILD_DIR}/libaom-${AOM_VERSION} && rm -rf ${IMAGICK_BUILD_DIR}/aom_build

# Compile libde265 (libheif dependency)
RUN curl -Ls -o libde265.tar.gz https://github.com/strukturag/libde265/releases/download/v${LIBDE265_VERSION}/libde265-${LIBDE265_VERSION}.tar.gz && tar xzf libde265.tar.gz && rm libde265.tar.gz \
    && cd ${IMAGICK_BUILD_DIR}/libde265-${LIBDE265_VERSION} \
    && ./configure --prefix ${INSTALL_DIR} --exec-prefix ${INSTALL_DIR} \
    && make -j $(nproc) && make install && rm -rf ${IMAGICK_BUILD_DIR}/libde265-${LIBDE265_VERSION}

# Compile libheif
RUN curl -Ls -o libheif.tar.gz https://github.com/strukturag/libheif/releases/download/v${LIBHEIF_VERSION}/libheif-${LIBHEIF_VERSION}.tar.gz \
    && tar xzf libheif.tar.gz && rm libheif.tar.gz && mkdir -p ${IMAGICK_BUILD_DIR}/libheif-${LIBHEIF_VERSION}/build && cd ${IMAGICK_BUILD_DIR}/libheif-${LIBHEIF_VERSION}/build \
    && cmake --preset=release-noplugins .. -DCMAKE_INSTALL_PREFIX=${INSTALL_DIR} \
    && make -j $(nproc) && make install && rm -rf ${IMAGICK_BUILD_DIR}/libheif-${LIBHEIF_VERSION}

# Compile ghostscript from source (the system package requires too many runtime files)
RUN curl -Ls -o ghostscript.tar.gz https://github.com/ArtifexSoftware/ghostpdl-downloads/releases/download/gs9561/ghostscript-${GS_VERSION}.tar.gz && tar xzf ghostscript.tar.gz && rm ghostscript.tar.gz \
    && mkdir -p ${IMAGICK_BUILD_DIR}/ghostscript-${GS_VERSION} && cd ${IMAGICK_BUILD_DIR}/ghostscript-${GS_VERSION} \
    && ./configure --prefix ${INSTALL_DIR} --exec-prefix ${INSTALL_DIR} --without-x \
    && make -j $(nproc) && cp bin/gs /tmp/gs && rm -rf ${IMAGICK_BUILD_DIR}/ghostscript-${GS_VERSION}

# Compile the ImageMagick library (AL2023 only has v6, we need v7)
RUN curl -Ls -o ImageMagick.tar.gz https://github.com/ImageMagick/ImageMagick/archive/refs/tags/${IMAGICK_VERSION}.tar.gz && tar xzf ImageMagick.tar.gz && rm ImageMagick.tar.gz \
    && cd ${IMAGICK_BUILD_DIR}/ImageMagick-${IMAGICK_VERSION} \
    && ./configure --prefix ${INSTALL_DIR} --exec-prefix ${INSTALL_DIR} --with-webp --with-heic --disable-static --with-freetype=yes \
    && make -j $(nproc) && make install && rm -rf ${IMAGICK_BUILD_DIR}/ImageMagick-${IMAGICK_VERSION} && convert -list configure

# Compile the php imagick extension and copy the dependencies
RUN curl -Ls -o imagick.tar.gz https://github.com/Imagick/imagick/archive/${IMAGICK_EXT_COMMIT}.tar.gz && tar xzf imagick.tar.gz && rm imagick.tar.gz \
    && cd imagick-${IMAGICK_EXT_COMMIT} \
    && phpize && ./configure --with-imagick=${INSTALL_DIR} \
    && make -j $(nproc) && make install && cp `php-config --extension-dir`/imagick.so /tmp/imagick.so && strip --strip-debug /tmp/imagick.so && echo 'extension=imagick.so' > /tmp/ext.ini \
    && php /bref/lib-copy/copy-dependencies.php /tmp/imagick.so /tmp/extension-libs

# Build the final image with just the files we need
FROM scratch

# Copy things we installed to the final image
COPY --from=ext /tmp/gs /opt/bin/gs
COPY --from=ext /tmp/imagick.so /opt/bref/extensions/imagick.so
COPY --from=ext /tmp/ext.ini /opt/bref/etc/php/conf.d/ext-imagick.ini
COPY --from=ext /tmp/extension-libs /opt/lib
