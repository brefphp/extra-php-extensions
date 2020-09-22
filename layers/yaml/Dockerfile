ARG PHP_VERSION
FROM bref/build-php-$PHP_VERSION as ext

ENV LIBYAML_BUILD_DIR=${BUILD_DIR}/libyaml

RUN set -xe; \
    mkdir -p ${LIBYAML_BUILD_DIR}; \
    # Download and upack the source code
    curl -Ls https://github.com/yaml/libyaml/archive/0.2.5.tar.gz  \
    | tar xzC ${LIBYAML_BUILD_DIR}

# Move into the unpackaged code directory
WORKDIR  ${LIBYAML_BUILD_DIR}/libyaml-0.2.5/

# Install
RUN set -xe; \
    ./bootstrap; \
    ./configure --prefix ${INSTALL_DIR} --exec-prefix ${INSTALL_DIR}; \
    make -j $(nproc); \
    make install

RUN pecl install yaml
RUN cp /opt/bref/lib/php/extensions/no-debug-zts-*/yaml.so /tmp/yaml.so

FROM scratch

COPY --from=ext /opt/bref/lib/libyaml-0.so.2 /opt/bref/lib64/libyaml-0.so.2
COPY --from=ext /opt/bref/lib/libyaml-0.so.2.0.9 /opt/bref/lib64/libyaml-0.so.2.0.9
COPY --from=ext /opt/bref/lib/libyaml.a /opt/bref/lib64/libyaml.a
COPY --from=ext /opt/bref/lib/libyaml.la /opt/bref/lib64/libyaml.la
COPY --from=ext /opt/bref/lib/libyaml.so /opt/bref/lib64/libyaml.so

COPY --from=ext /tmp/yaml.so /opt/bref-extra/yaml.so
