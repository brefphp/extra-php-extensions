ARG PHP_VERSION
FROM bref/build-php-$PHP_VERSION:1.0.0 AS ext

# Fix library path
ENV LD_LIBRARY_PATH=/usr/lib:/usr/lib64:$LD_LIBRARY_PATH

# Install packages
RUN yum install -y gmp-devel

# Build PHP extension
WORKDIR /tmp/build/php/ext/gmp
RUN phpize && \
    ./configure \
    --build=x86_64-pc-linux-gnu \
    --prefix=${INSTALL_DIR} \
    --enable-option-checking=fatal

RUN make -j $(nproc) install
RUN cp /opt/bref/lib/php/extensions/no-debug-non-zts-*/gmp.so /tmp/gmp.so
RUN echo 'extension=/opt/bref-extra/gmp.so' > /tmp/ext.ini


# Build the final image with just the files we need
FROM scratch

# Copy things we installed to the final image
COPY --from=ext /usr/include/gmp-mparam-x86_64.h /opt/bref/include/gmp-mparam-x86_64.h
COPY --from=ext /usr/include/gmp-mparam.h /opt/bref/include/gmp-mparam.h
COPY --from=ext /usr/include/gmp-x86_64.h /opt/bref/include/gmp-x86_64.h
COPY --from=ext /usr/include/gmp.h /opt/bref/include/gmp.h
COPY --from=ext /usr/include/gmpxx.h /opt/bref/include/gmpxx.h

COPY --from=ext /usr/lib64/libgmp.so /opt/bref/lib64/libgmp.so
COPY --from=ext /usr/lib64/libgmpxx.so /opt/bref/lib64/libgmpxx.so

COPY --from=ext /tmp/gmp.so /opt/bref-extra/gmp.so
COPY --from=ext /tmp/ext.ini /opt/bref/etc/php/conf.d/ext-gmp.ini
