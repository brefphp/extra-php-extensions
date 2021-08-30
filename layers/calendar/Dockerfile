ARG PHP_VERSION
FROM bref/build-php-$PHP_VERSION:1.2.13 AS ext

WORKDIR ${PHP_BUILD_DIR}/ext/calendar
RUN phpize && \
    ./configure \
    --enable-calendar

RUN make -j $(nproc)
RUN make install
RUN cp "$(php-config --extension-dir)/calendar.so" /tmp/calendar.so
RUN echo 'extension=/opt/bref-extra/calendar.so' > /tmp/ext.ini

FROM scratch

COPY --from=ext /tmp/calendar.so /opt/bref-extra/calendar.so
COPY --from=ext /tmp/ext.ini /opt/bref/etc/php/conf.d/ext-calendar.ini
