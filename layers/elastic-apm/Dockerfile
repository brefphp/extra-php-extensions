ARG PHP_VERSION
FROM bref/build-php-$PHP_VERSION:1.5.4 AS ext

RUN \
    LD_LIBRARY_PATH=/lib64:/lib yum install -y libcurl-devel openssl-devel \
    && mkdir /tmp/apm \
    && curl -L https://github.com/elastic/apm-agent-php/archive/refs/tags/v1.4.2.tar.gz | tar -C /tmp/apm -zx --strip-components=1 \
    && cd /tmp/apm/src/ext \
    && phpize \
    && CFLAGS="-std=gnu99" ./configure --enable-elastic_apm \
    && make clean \
    && make \
    && make install \
    && echo 'extension=/opt/bref-extra/elastic_apm.so' > /tmp/elastic-apm.ini \
    && echo 'elastic_apm.bootstrap_php_part_file=/opt/bref-extra/elastic_apm/bootstrap_php_part.php' > /tmp/elastic-apm.ini

# Build the final image from the scratch image that contain files you want to export
FROM scratch

# Copy the two key files to the correct location for the empty layer.
COPY --from=ext /tmp/apm/src/ext/modules/elastic_apm.so /opt/bref-extra/elastic_apm.so
COPY --from=ext /tmp/elastic-apm.ini /opt/bref/etc/php/conf.d/elastic-apm.ini

# The Elastic APM also contains some PHP that needs to be
# present in the layer in addition to the extension
COPY --from=ext /tmp/apm/src/ElasticApm /opt/bref-extra/elastic_apm/ElasticApm
COPY --from=ext /tmp/apm/src/bootstrap_php_part.php /opt/bref-extra/elastic_apm/bootstrap_php_part.php