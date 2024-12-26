ARG PHP_VERSION
ARG BREF_VERSION
FROM bref/build-php-$PHP_VERSION:$BREF_VERSION AS ext

RUN \
    LD_LIBRARY_PATH=/lib64:/lib yum install -y libcurl-devel openssl-devel \
    && mkdir /tmp/apm \
    && curl -L https://github.com/elastic/apm-agent-php/archive/refs/tags/v1.8.4.tar.gz | tar -C /tmp/apm -zx --strip-components=1 \
    && cd /tmp/apm/src/ext \
    && phpize \
    && CFLAGS="-std=gnu99" ./configure --enable-elastic_apm \
    && make clean \
    && make \
    && make install \
    && echo 'extension=elastic_apm.so' > /tmp/elastic-apm.ini \
    && echo "elastic_apm.bootstrap_php_part_file=/opt/bref/etc/elastic-apm-agent-php-src/bootstrap_php_part.php" >> /tmp/elastic-apm.ini

# Build the final image from the scratch image that contain files you want to export
FROM scratch

# Copy the two key files to the correct location for the empty layer.
COPY --from=ext /tmp/apm/src/ext/modules/elastic_apm.so /opt/bref/extensions/elastic_apm.so
COPY --from=ext /tmp/elastic-apm.ini /opt/bref/etc/php/conf.d/elastic-apm.ini
COPY --from=ext /tmp/apm/src/ /opt/bref/etc/elastic-apm-agent-php-src/
