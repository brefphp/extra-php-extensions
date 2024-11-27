ARG PHP_VERSION
ARG BREF_VERSION
FROM bref/build-php-$PHP_VERSION:$BREF_VERSION AS ext
ARG PHP_VERSION

# Prepare environment
ENV H3_BUILD_DIR=${BUILD_DIR}/h3
RUN mkdir -p ${H3_BUILD_DIR}

# Compile h3
WORKDIR ${H3_BUILD_DIR}
RUN git clone https://github.com/uber/h3.git && \
    cd h3 && \
    git checkout v3.7.2 && \
    cmake -DBUILD_SHARED_LIBS=ON . && \
    make -j "$(nproc)" && \
    make install

# Compile the php h3 extension
WORKDIR ${H3_BUILD_DIR}/h3-php
RUN curl -L https://github.com/abler98/h3-php/archive/refs/tags/v1.0.0.tar.gz -o h3.tar.gz && \
    tar xzf h3.tar.gz && \
    cd h3-php-1.0.0 && \
    phpize && \
    ./configure --with-h3 && \
    make && \
    make install

RUN cp `php-config --extension-dir`/h3.so /tmp/h3.so
RUN strip --strip-debug /tmp/h3.so
RUN echo 'extension=h3.so' > /tmp/ext.ini

RUN php /bref/lib-copy/copy-dependencies.php /tmp/h3.so /tmp/extension-libs

# Build the final image with just the files we need
FROM scratch

# Copy things we installed to the final image
COPY --from=ext /tmp/h3.so /opt/bref/extensions/h3.so
COPY --from=ext /tmp/ext.ini /opt/bref/etc/php/conf.d/ext-h3.ini
COPY --from=ext /tmp/extension-libs /opt/lib
