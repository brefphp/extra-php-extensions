ARG PHP_VERSION
FROM bref/build-php-$PHP_VERSION:1.1.4 AS ext

ENV LIBRABBITMQ_BUILD_DIR=${BUILD_DIR}/librabbitmq

RUN set -xe; \
    mkdir -p ${LIBRABBITMQ_BUILD_DIR}; \
    # Download and upack the source code
    curl -Ls  https://github.com/alanxz/rabbitmq-c/archive/v0.9.0.tar.gz  \
    | tar xzC ${LIBRABBITMQ_BUILD_DIR}

# Move into the unpackaged code directory
WORKDIR  ${LIBRABBITMQ_BUILD_DIR}/rabbitmq-c-0.9.0/

# Install
RUN set -xe; \
    cmake -DCMAKE_INSTALL_PREFIX=${INSTALL_DIR} . && \
    cmake --build . --target install

RUN pecl install amqp
RUN cp /opt/bref/lib/php/extensions/no-debug-non-zts-*/amqp.so /tmp/amqp.so
RUN echo 'extension=/opt/bref-extra/amqp.so' > /tmp/ext.ini

# Build the final image with just the files we need
FROM scratch

# Copy things we installed to the final image
COPY --from=ext /opt/bref/include/amqp.h /opt/bref/include/amqp.h
COPY --from=ext /opt/bref/include/amqp_framing.h /opt/bref/include/amqp_framing.h
COPY --from=ext /opt/bref/include/amqp_ssl_socket.h /opt/bref/include/amqp_ssl_socket.h
COPY --from=ext /opt/bref/include/amqp_tcp_socket.h /opt/bref/include/amqp_tcp_socket.h

COPY --from=ext /opt/bref/lib64/librabbitmq.a /opt/bref/lib64/librabbitmq.a
COPY --from=ext /opt/bref/lib64/librabbitmq.so /opt/bref/lib64/librabbitmq.so
COPY --from=ext /opt/bref/lib64/librabbitmq.so.4 /opt/bref/lib64/librabbitmq.so.4
COPY --from=ext /opt/bref/lib64/librabbitmq.so.4.3.0 /opt/bref/lib64/librabbitmq.so.4.3.0
COPY --from=ext /opt/bref/lib64/pkgconfig/librabbitmq.pc /opt/bref/lib64/pkgconfig/librabbitmq.pc

COPY --from=ext /tmp/amqp.so /opt/bref-extra/amqp.so
COPY --from=ext /tmp/ext.ini /opt/bref/etc/php/conf.d/ext-amqp.ini
