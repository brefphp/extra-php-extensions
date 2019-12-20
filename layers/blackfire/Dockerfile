ARG PHP_VERSION
FROM bref/build-php-$PHP_VERSION AS ext

RUN version=$(php -r "echo PHP_MAJOR_VERSION.PHP_MINOR_VERSION;")-zts \
    && curl -A "Docker" -o /tmp/blackfire-probe.tar.gz -D - -L -s https://blackfire.io/api/v1/releases/probe/php/linux/amd64/$version \
    && mkdir -p /tmp/blackfire \
    && tar zxpf /tmp/blackfire-probe.tar.gz -C /tmp/blackfire \
    && cp /tmp/blackfire/blackfire-*.so /tmp/blackfire.so \
    && rm -rf /tmp/blackfire /tmp/blackfire-probe.tar.gz


# Build the final image from the lambci image that is close to the production environment
FROM lambci/lambda:provided

# Copy things we installed to the final image
COPY --from=ext /tmp/blackfire.so /opt/bref-extra/blackfire.so
