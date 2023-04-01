ARG PHP_VERSION
ARG BREF_VERSION
FROM bref/build-php-$PHP_VERSION:$BREF_VERSION AS ext

RUN set -ex && \
    LD_LIBRARY_PATH= && \
    yum -y install bzip2-devel && \
    pecl channel-update pecl.php.net && \
    yes '' | pecl install bsdiff-stable && \
    cp "$(php-config --extension-dir)/bsdiff.so" /tmp/bsdiff.so && \
    echo 'extension=bsdiff.so' > /tmp/ext.ini

# Build the final image with just the files we need
FROM scratch

# Copy things we installed to the final image
COPY --from=ext /tmp/bsdiff.so  /opt/bref/extensions/bsdiff.so
COPY --from=ext /tmp/ext.ini /opt/bref/etc/php/conf.d/ext-bsdiff.ini
