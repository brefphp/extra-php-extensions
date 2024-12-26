ARG PHP_VERSION
ARG BREF_VERSION
FROM bref/build-php-$PHP_VERSION:$BREF_VERSION AS ext
ARG PHP_VERSION

# Prepare environment
ENV AMQP_BUILD_DIR=${BUILD_DIR}/amqp
RUN mkdir -p ${AMQP_BUILD_DIR}

# Compile rabbitmq
WORKDIR ${AMQP_BUILD_DIR}
# Currently it is not possible to use newer rabbitmq-c version than 0.13.0 because this is
# last version which supports CMake 3.17 which is latest available version in Amazon Linux 2.
RUN curl -Ls -o rabbitmq-c.tar.gz https://github.com/alanxz/rabbitmq-c/archive/refs/tags/v0.13.0.tar.gz
RUN tar xzf rabbitmq-c.tar.gz
WORKDIR ${AMQP_BUILD_DIR}/rabbitmq-c-0.13.0
RUN cmake -DCMAKE_INSTALL_PREFIX=${INSTALL_DIR} .
RUN cmake --build . --target install

# Compile the php amqp extension
WORKDIR ${AMQP_BUILD_DIR}
RUN git clone https://github.com/php-amqp/php-amqp
WORKDIR ${AMQP_BUILD_DIR}/php-amqp
RUN git checkout v2.1.2
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
