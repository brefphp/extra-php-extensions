ARG PHP_VERSION
ARG BREF_VERSION
FROM bref/build-php-$PHP_VERSION:$BREF_VERSION AS ext
ARG PHP_VERSION

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

RUN if [[ "$PHP_VERSION" = "80" ]] ; then pecl install amqp-1.11.0beta ; else pecl install amqp ; fi
RUN cp `php-config --extension-dir`/amqp.so /tmp/amqp.so
RUN echo 'extension=amqp.so' > /tmp/ext.ini

RUN php /bref/lib-copy/copy-dependencies.php /tmp/amqp.so /tmp/extension-libs


# Build the final image with just the files we need
FROM scratch

# Copy things we installed to the final image
COPY --from=ext /tmp/amqp.so /opt/bref/extensions/amqp.so
COPY --from=ext /tmp/ext.ini /opt/bref/etc/php/conf.d/ext-amqp.ini
COPY --from=ext /tmp/extension-libs /opt/lib
