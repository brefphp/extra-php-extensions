ARG PHP_VERSION
ARG BREF_VERSION
FROM bref/build-php-$PHP_VERSION:$BREF_VERSION AS ext

RUN git clone https://github.com/NoiseByNorthwest/php-spx.git \
    && cd php-spx \
    && git reset --hard v0.4.10 \
    && phpize \
    && ./configure --prefix ${INSTALL_DIR} --exec-prefix ${INSTALL_DIR} \
    && make -j $(nproc) \
    && make install \
    && cd ../ \
    && rm -rf php-spx

RUN cp `php-config --extension-dir`/spx.so /tmp/spx.so
RUN cp -r ${INSTALL_DIR}/share/misc/php-spx/assets /tmp/spx-assets
RUN echo 'extension=spx.so' > /tmp/ext.ini


# Build the final image with just the files we need
FROM scratch

# Copy things we installed to the final image
COPY --from=ext /tmp/spx.so /opt/bref/extensions/spx.so
COPY --from=ext /tmp/spx-assets /opt/bref/share/misc/php-spx/assets
COPY --from=ext /tmp/ext.ini /opt/bref/etc/php/conf.d/ext-spx.ini
