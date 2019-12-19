ARG PHP_VERSION
FROM bref/build-php-$PHP_VERSION

ENV LIBRABBITMQ_BUILD_DIR=${BUILD_DIR}/librabbitmq

RUN set -xe; \
    mkdir -p ${LIBRABBITMQ_BUILD_DIR}; \
# Download and upack the source code
    curl -Ls  https://github.com/alanxz/rabbitmq-c/archive/v0.9.0.tar.gz  \
  | tar xzC ${LIBRABBITMQ_BUILD_DIR}

# Move into the unpackaged code directory
WORKDIR  ${LIBRABBITMQ_BUILD_DIR}/rabbitmq-c-0.9.0/

# Install"
RUN set -xe; \
    cmake -DCMAKE_INSTALL_PREFIX=${INSTALL_DIR} . && \
    cmake --build . --target install

RUN pecl install amqp

