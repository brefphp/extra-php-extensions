ARG PHP_VERSION
ARG BREF_VERSION
FROM bref/build-php-$PHP_VERSION:$BREF_VERSION AS ext

ENV V8JS_BUILD_DIR=${BUILD_DIR}/v8js
ENV V8_VERSION=12.0.267.36
ENV V8JS_COMMIT=8a39efa3cf3b275e402ddf3c4f6b611a5f69a499
ENV V8_DIR=${INSTALL_DIR}/v8

RUN mkdir -p ${V8JS_BUILD_DIR}
WORKDIR ${V8JS_BUILD_DIR}

RUN LD_LIBRARY_PATH= dnf -y install \
    gcc \
    gcc-c++ \
    git \
    glib2-devel \
    make \
    ninja-build \
    patchelf \
    pkgconf-pkg-config \
    python3 \
    which

RUN git clone --depth 1 https://chromium.googlesource.com/chromium/tools/depot_tools.git ${V8JS_BUILD_DIR}/depot_tools
ENV PATH="${V8JS_BUILD_DIR}/depot_tools:${PATH}"
ENV DEPOT_TOOLS_UPDATE=0

RUN fetch v8
WORKDIR ${V8JS_BUILD_DIR}/v8
RUN git checkout ${V8_VERSION} && gclient sync -D
RUN tools/dev/v8gen.py -vv x64.release -- is_component_build=true use_custom_libcxx=false
RUN ninja -C out.gn/x64.release/

RUN mkdir -p ${V8_DIR}/lib ${V8_DIR}/include && \
    cp out.gn/x64.release/lib*.so out.gn/x64.release/*_blob.bin out.gn/x64.release/icudtl.dat ${V8_DIR}/lib/ && \
    cp -R include/* ${V8_DIR}/include/ && \
    for f in ${V8_DIR}/lib/*.so; do patchelf --set-rpath '$ORIGIN' "$f"; done

WORKDIR ${V8JS_BUILD_DIR}
RUN curl -Ls https://github.com/phpv8/v8js/archive/${V8JS_COMMIT}.tar.gz | tar xz
WORKDIR ${V8JS_BUILD_DIR}/v8js-${V8JS_COMMIT}
RUN phpize && \
    ./configure \
        --with-v8js=${V8_DIR} \
        LDFLAGS="-lstdc++ -Wl,-rpath,${V8_DIR}/lib" \
        CPPFLAGS="-DV8_COMPRESS_POINTERS -DV8_ENABLE_SANDBOX" && \
    make -j "$(nproc)" && \
    make install

RUN cp "$(php-config --extension-dir)"/v8js.so /tmp/v8js.so
RUN strip --strip-debug /tmp/v8js.so
RUN echo 'extension=v8js.so' > /tmp/ext.ini

RUN php /bref/lib-copy/copy-dependencies.php /tmp/v8js.so /tmp/extension-libs
RUN cp ${V8_DIR}/lib/libv8*.so ${V8_DIR}/lib/icudtl.dat ${V8_DIR}/lib/*_blob.bin /tmp/extension-libs/
RUN cp -L /lib64/libstdc++.so.6 /lib64/libgcc_s.so.1 /tmp/extension-libs/
RUN patchelf --set-rpath '$ORIGIN/../../lib' /tmp/v8js.so
RUN for f in /tmp/extension-libs/*.so; do patchelf --set-rpath '$ORIGIN' "$f"; done

FROM scratch

COPY --from=ext /tmp/v8js.so /opt/bref/extensions/v8js.so
COPY --from=ext /tmp/ext.ini /opt/bref/etc/php/conf.d/ext-v8js.ini
COPY --from=ext /opt/v8 /opt/v8
COPY --from=ext /tmp/extension-libs /opt/lib
