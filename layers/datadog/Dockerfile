ARG PHP_VERSION
FROM bref/build-php-$PHP_VERSION:1.7.14 AS ext

ENV DDTRACE_BUILD_DIR=${BUILD_DIR}/ddtrace

RUN set -xe; \
    mkdir -p ${DDTRACE_BUILD_DIR}; \
    curl -Ls -o ${DDTRACE_BUILD_DIR}/datadog-setup.php \
      https://github.com/DataDog/dd-trace-php/releases/latest/download/datadog-setup.php

WORKDIR ${DDTRACE_BUILD_DIR}

RUN php datadog-setup.php --php-bin=all

RUN cp "$(php-config --extension-dir)/ddtrace.so" /tmp/ddtrace.so
RUN cp "$(php-config --extension-dir)/ddappsec.so" /tmp/ddappsec.so
RUN cp "$(php-config --extension-dir)/datadog-profiling.so" /tmp/datadog-profiling.so
RUN cp "$(php-config --ini-dir)/98-ddtrace.ini" /tmp/ext.ini

RUN sed -i 's/extension = ddtrace\.so/extension = \/opt\/bref-extra\/ddtrace.so/' /tmp/ext.ini
RUN sed -i 's/extension = ddappsec\.so/extension = \/opt\/bref-extra\/ddappsec.so/' /tmp/ext.ini
RUN sed -i 's/extension = datadog-profiling\.so/;extension = \/opt\/bref-extra\/datadog-profiling.so/' /tmp/ext.ini
RUN sed -i 's/datadog\.appsec\.enabled = On/datadog.appsec.enabled = Off/' /tmp/ext.ini

FROM scratch

COPY --from=ext /tmp/ddtrace.so /opt/bref-extra/ddtrace.so
COPY --from=ext /tmp/ddappsec.so /opt/bref-extra/ddappsec.so
COPY --from=ext /tmp/datadog-profiling.so /opt/bref-extra/datadog-profiling.so
COPY --from=ext /tmp/ext.ini /opt/bref/etc/php/conf.d/98-ddtrace.ini
COPY --from=ext /opt/datadog/ /opt/datadog

# This adds the Datadog Agent to the layer.
# Refer to https://gallery.ecr.aws/datadog/lambda-extension
COPY --from=public.ecr.aws/datadog/lambda-extension:latest /opt/extensions/ /opt/extensions
