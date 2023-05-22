ARG PHP_VERSION
ARG BREF_VERSION
FROM bref/build-php-$PHP_VERSION:$BREF_VERSION AS ext
ARG PHP_VERSION

# Prepare environment
ENV AMQP_BUILD_DIR=${BUILD_DIR}/amqp
RUN mkdir -p ${AMQP_BUILD_DIR}

# Compile rabbitmq
WORKDIR ${AMQP_BUILD_DIR}
RUN curl -Ls -o rabbitmq-c.tar.gz https://github.com/alanxz/rabbitmq-c/archive/refs/tags/v0.13.0.tar.gz
RUN tar xzf rabbitmq-c.tar.gz
WORKDIR ${AMQP_BUILD_DIR}/rabbitmq-c-0.13.0
RUN cmake -DCMAKE_INSTALL_PREFIX=${INSTALL_DIR} .
RUN cmake --build . --target install

# Compile the php amqp extension
WORKDIR ${AMQP_BUILD_DIR}
RUN git clone https://github.com/php-amqp/php-amqp
WORKDIR ${AMQP_BUILD_DIR}/php-amqp
RUN git reset --hard 618e06ad2ef867598831cdd3faadba0dd65be917
RUN phpize
RUN ./configure
RUN make -j $(nproc)
RUN make install

RUN cp `php-config --extension-dir`/amqp.so /tmp/amqp.so
RUN strip --strip-debug /tmp/amqp.so
RUN echo 'extension=amqp.so' > /tmp/ext.ini

RUN php /bref/lib-copy/copy-dependencies.php /tmp/amqp.so /tmp/extension-libs

# Build the final image with just the files we need
FROM scratch

# Copy things we installed to the final image
COPY --from=ext /tmp/amqp.so /opt/bref/extensions/amqp.so
COPY --from=ext /tmp/ext.ini /opt/bref/etc/php/conf.d/ext-amqp.ini
COPY --from=ext /tmp/extension-libs /opt/lib
