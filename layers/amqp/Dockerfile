ARG PHP_VERSION
FROM bref/build-php-$PHP_VERSION

ENV LIBRABBITMQ_BUILD_DIR=${BUILD_DIR}/librabbitmq

RUN set -xe; \
    mkdir -p ${LIBRABBITMQ_BUILD_DIR}; \
# Download and upack the source code
    curl -Ls  https://github.com/alanxz/rabbitmq-c/archive/v0.9.0.tar.gz  \
  | tar xzC ${LIBRABBITMQ_BUILD_DIR}

RUN echo ${LIBRABBITMQ_BUILD_DIR}
# Move into the unpackaged code directory
WORKDIR  ${LIBRABBITMQ_BUILD_DIR}/rabbitmq-c-0.9.0/

# Install"
RUN set -xe; \
    cmake -DCMAKE_INSTALL_PREFIX=${INSTALL_DIR} . && \
    cmake --build . --target install

RUN pecl install amqp

# Build the final image from the lambci image that is close to the production environment
FROM lambci/lambda:provided
COPY --from=0 /opt/bref/lib/php/extensions/no-debug-zts-20180731/amqp.so /opt/bref-aqmp/amqp.so