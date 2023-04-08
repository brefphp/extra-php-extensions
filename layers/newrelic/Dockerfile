ARG CPU_PREFIX
ARG PHP_VERSION
ARG BREF_VERSION
FROM bref/${CPU_PREFIX}build-php-$PHP_VERSION:$BREF_VERSION AS ext

ARG NEWRELIC_VERSION=10.9.0.324

ENV LD_LIBRARY_PATH=/usr/lib:/usr/lib64:$LD_LIBRARY_PATH
ENV RELIC_BUILD_DIR=${BUILD_DIR}/relic
RUN mkdir -p ${RELIC_BUILD_DIR}
WORKDIR ${RELIC_BUILD_DIR}

RUN yum update -y &&  \
    yum install -y git \
    amazon-linux-extras \
    libcurl-devel \
    openssl-devel  \
    openssl-static \
    pcre-devel  \
    pcre-static \
    zlib-devel  \
    zlib-static

RUN amazon-linux-extras install -y epel
RUN amazon-linux-extras install -y golang1.11

RUN git clone https://github.com/newrelic/newrelic-php-agent.git . \
    && git reset --hard v${NEWRELIC_VERSION}

RUN make \
    && make agent-install

RUN cp `php-config --extension-dir`/newrelic.so /tmp/newrelic.so
RUN echo 'extension=newrelic.so' > /tmp/ext.ini

RUN php /bref/lib-copy/copy-dependencies.php /tmp/newrelic.so /tmp/extension-libs

## Build the final image from the scratch image that contain files you want to export
FROM scratch

# Copy the two key files to the correct location for the empty layer.
# newrelic.so should become a layered extension.
COPY --from=ext /tmp/newrelic.so /opt/bref/extensions/newrelic.so
COPY --from=ext /tmp/ext.ini /opt/bref/etc/php/conf.d/ext-newrelic.ini
COPY --from=ext /tmp/extension-libs /opt/lib
