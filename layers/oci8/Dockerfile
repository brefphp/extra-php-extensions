ARG CPU_PREFIX
ARG PHP_VERSION
ARG BREF_VERSION
FROM bref/${CPU_PREFIX}build-php-$PHP_VERSION:$BREF_VERSION AS ext

# Specify library path
ENV LD_LIBRARY_PATH=/usr/lib:/usr/lib64:$LD_LIBRARY_PATH
ENV ORACLE_BUILD_DIR=${BUILD_DIR}/oracle

# Install libaio
RUN LD_LIBRARY_PATH=/lib64:/lib yum install -y libaio

RUN mkdir -p $ORACLE_BUILD_DIR
WORKDIR ${ORACLE_BUILD_DIR}

COPY install.sh ${ORACLE_BUILD_DIR}/install.sh
RUN chmod +x ${ORACLE_BUILD_DIR}/install.sh
RUN ${ORACLE_BUILD_DIR}/install.sh

RUN cp `php-config --extension-dir`/oci8.so /tmp/oci8.so
RUN echo 'extension=oci8.so' > /tmp/ext.ini
RUN php /bref/lib-copy/copy-dependencies.php /tmp/oci8.so /tmp/extension-libs

# Build the final image from the scratch image that contain files you want to export
FROM scratch

COPY --from=ext /tmp/oci8.so /opt/bref/extensions/oci8.so
COPY --from=ext /tmp/ext.ini /opt/bref/etc/php/conf.d/ext-oci8.ini
COPY --from=ext /tmp/extension-libs /opt/lib
