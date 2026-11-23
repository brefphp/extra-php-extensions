ARG PHP_VERSION
ARG BREF_VERSION
FROM bref/build-php-$PHP_VERSION:$BREF_VERSION AS ext

# Excimer moved from PECL to PIE in 1.2.7, so PECL no longer has the latest release.
ENV EXCIMER_VERSION=1.2.7
WORKDIR /tmp
RUN curl -Ls -o excimer.tar.gz https://github.com/wikimedia/mediawiki-php-excimer/archive/refs/tags/${EXCIMER_VERSION}.tar.gz && \
    tar xzf excimer.tar.gz && mv mediawiki-php-excimer-${EXCIMER_VERSION} excimer
WORKDIR /tmp/excimer
RUN phpize
RUN ./configure
RUN make -j $(nproc)
RUN make install

RUN cp `php-config --extension-dir`/excimer.so /tmp/excimer.so
RUN echo 'extension=excimer.so' > /tmp/ext.ini

# Build the final image with just the files we need
FROM scratch

# Copy things we installed to the final image
COPY --from=ext /tmp/excimer.so /opt/bref/extensions/excimer.so
COPY --from=ext /tmp/ext.ini /opt/bref/etc/php/conf.d/ext-excimer.ini
