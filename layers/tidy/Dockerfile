ARG PHP_VERSION
FROM bref/build-php-$PHP_VERSION AS ext

ENV LD_LIBRARY_PATH=/usr/lib:/usr/lib64:$LD_LIBRARY_PATH
RUN yum -y install amazon-linux-extras
RUN amazon-linux-extras install epel -y
RUN yum install -y libtidy-devel
RUN yum install -y php-tidy
WORKDIR ${PHP_BUILD_DIR}/ext/tidy
RUN phpize
RUN ./configure --with-tidy --with-libdir=lib64
RUN make -j `nproc` && make install
RUN cp `php-config --extension-dir`/tidy.so /tmp/tidy.so
RUN echo 'extension=/opt/bref-extra/tidy.so' > /tmp/ext.ini

# Build the final image with just the files we need
FROM scratch

# Copy things we installed to the final image
COPY --from=ext /usr/lib64/libtidy* /opt/bref/lib64/
COPY --from=ext /tmp/tidy.so /opt/bref-extra/tidy.so
COPY --from=ext /tmp/ext.ini /opt/bref/etc/php/conf.d/ext-tidy.ini

