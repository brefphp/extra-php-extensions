ARG PHP_VERSION
ARG BREF_VERSION
FROM bref/build-php-$PHP_VERSION:$BREF_VERSION AS ext

# Fix library path
ENV LD_LIBRARY_PATH=/usr/lib:/usr/lib64:$LD_LIBRARY_PATH

# Install packages
RUN yum install -y gmp-devel

# Build PHP extension
WORKDIR ${PHP_BUILD_DIR}/ext/gmp
RUN phpize && \
    ./configure \
    --build=x86_64-pc-linux-gnu \
    --prefix=${INSTALL_DIR} \
    --enable-option-checking=fatal

RUN make -j $(nproc) install
RUN cp `php-config --extension-dir`/gmp.so /tmp/gmp.so
RUN echo 'extension=gmp.so' > /tmp/ext.ini

RUN php /bref/lib-copy/copy-dependencies.php /tmp/gmp.so /tmp/extension-libs


# Build the final image with just the files we need
FROM scratch

# Copy things we installed to the final image
COPY --from=ext /tmp/gmp.so /opt/bref/extensions/gmp.so
COPY --from=ext /tmp/ext.ini /opt/bref/etc/php/conf.d/ext-gmp.ini
COPY --from=ext /tmp/extension-libs /opt/lib
