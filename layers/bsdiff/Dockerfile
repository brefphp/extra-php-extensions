ARG PHP_VERSION
FROM bref/build-php-$PHP_VERSION:1.7.14 AS ext

RUN set -ex && \
    LD_LIBRARY_PATH= && \
    yum -y install bzip2-devel && \
    pecl channel-update pecl.php.net && \
    yes '' | pecl install bsdiff-stable && \
    cp "$(php-config --extension-dir)/bsdiff.so" /tmp/bsdiff.so && \
    echo 'extension=/opt/bref-extra/bsdiff.so' > /tmp/bsdiff.ini

# Copy things we installed to the final image
FROM scratch

COPY --from=ext /tmp/bsdiff.so  /opt/bref-extra/bsdiff.so
COPY --from=ext /tmp/bsdiff.ini /opt/bref/etc/php/conf.d/ext-bsdiff.ini
