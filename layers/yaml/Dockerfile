ARG PHP_VERSION
ARG BREF_VERSION
FROM bref/build-php-$PHP_VERSION:$BREF_VERSION AS ext

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
RUN cp `php-config --extension-dir`/yaml.so /tmp/yaml.so
RUN echo 'extension=yaml.so' > /tmp/ext.ini

RUN php /bref/lib-copy/copy-dependencies.php /tmp/yaml.so /tmp/extension-libs


FROM scratch

COPY --from=ext /tmp/yaml.so /opt/bref/extensions/yaml.so
COPY --from=ext /tmp/ext.ini /opt/bref/etc/php/conf.d/ext-yaml.ini
COPY --from=ext /tmp/extension-libs /opt/lib
