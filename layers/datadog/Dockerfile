ARG PHP_VERSION
ARG BREF_VERSION
FROM bref/build-php-$PHP_VERSION:$BREF_VERSION AS ext

ENV DDTRACE_BUILD_DIR=${BUILD_DIR}/ddtrace

RUN set -xe; \
    mkdir -p ${DDTRACE_BUILD_DIR}; \
    curl -Ls -o ${DDTRACE_BUILD_DIR}/datadog-setup.php \
      https://github.com/DataDog/dd-trace-php/releases/download/0.87.2/datadog-setup.php

WORKDIR ${DDTRACE_BUILD_DIR}

RUN php datadog-setup.php --php-bin=all --enable-profiling

RUN cp "$(php-config --extension-dir)/ddtrace.so" /tmp/ddtrace.so && \
    cp "$(php-config --extension-dir)/datadog-profiling.so" /tmp/datadog-profiling.so && \
    cp "$(php-config --ini-dir)/98-ddtrace.ini" /tmp/ext.ini

RUN sed -i 's/extension = ddtrace\.so/extension = \/opt\/bref-extra\/ddtrace.so/' /tmp/ext.ini && \
    sed -i 's/extension = datadog-profiling\.so/extension = \/opt\/bref-extra\/datadog-profiling.so/' /tmp/ext.ini && \
    sed -i 's/;datadog\.agent_host.*$/datadog\.agent_host = 127.0.0.1/' /tmp/ext.ini && \
    sed -i 's/^.*datadog\.appsec\.enabled.*$/datadog.appsec.enabled = Off/' /tmp/ext.ini && \
    sed -i 's/datadog\.profiling\.enabled = On/datadog.profiling.enabled = 1/' /tmp/ext.ini

# Build the final image with just the files we need
FROM scratch

# Copy things we installed to the final image
COPY --from=ext /tmp/ddtrace.so /opt/bref-extra/ddtrace.so
COPY --from=ext /tmp/datadog-profiling.so /opt/bref-extra/datadog-profiling.so
COPY --from=ext /tmp/ext.ini /opt/bref/etc/php/conf.d/98-ddtrace.ini
COPY --from=ext /opt/datadog/ /opt/datadog
