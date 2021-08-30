ARG PHP_VERSION
FROM bref/build-php-$PHP_VERSION:1.2.13 AS ext

RUN version=$(php -r "echo PHP_MAJOR_VERSION.PHP_MINOR_VERSION;") \
    && curl -A "Docker" -o /tmp/blackfire-probe.tar.gz -D - -L -s https://blackfire.io/api/v1/releases/probe/php/linux/amd64/$version \
    && mkdir -p /tmp/blackfire \
    && tar zxpf /tmp/blackfire-probe.tar.gz -C /tmp/blackfire \
    && cp /tmp/blackfire/blackfire-*.so /tmp/blackfire.so \
    && rm -rf /tmp/blackfire /tmp/blackfire-probe.tar.gz

RUN echo 'extension=/opt/bref-extra/blackfire.so' > /tmp/ext.ini


# Build the final image with just the files we need
FROM scratch

# Copy things we installed to the final image
COPY --from=ext /tmp/blackfire.so /opt/bref-extra/blackfire.so
COPY --from=ext /tmp/ext.ini /opt/bref/etc/php/conf.d/ext-blackfire.ini
