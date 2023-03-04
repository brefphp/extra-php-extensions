ARG PHP_VERSION
ARG BREF_VERSION
FROM bref/build-php-$PHP_VERSION:$BREF_VERSION AS ext

WORKDIR ${PHP_BUILD_DIR}/ext/calendar
RUN phpize && \
    ./configure \
    --enable-calendar

RUN make -j $(nproc)
RUN make install
RUN cp "$(php-config --extension-dir)/calendar.so" /tmp/calendar.so
RUN echo 'extension=calendar.so' > /tmp/ext.ini

FROM scratch

COPY --from=ext /tmp/calendar.so /opt/bref/extensions/calendar.so
COPY --from=ext /tmp/ext.ini /opt/bref/etc/php/conf.d/ext-calendar.ini
