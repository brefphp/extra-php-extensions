ARG PHP_VERSION
FROM bref/build-php-$PHP_VERSION:1.3.3 AS ext
ARG PHP_VERSION

# Specify library path
ENV LD_LIBRARY_PATH=/usr/lib:/usr/lib64:$LD_LIBRARY_PATH
ENV ORACLE_BUILD_DIR=${BUILD_DIR}/oracle

# Install libaio
RUN LD_LIBRARY_PATH=/lib64:/lib yum install -y libaio

RUN mkdir -p ${ORACLE_BUILD_DIR}; \
    cd ${ORACLE_BUILD_DIR}; \
    curl -o oci-basic.zip https://download.oracle.com/otn_software/linux/instantclient/213000/instantclient-basiclite-linux.x64-21.3.0.0.0.zip && \
    unzip oci-basic.zip -d src && \
    curl -o oci-sdk.zip https://download.oracle.com/otn_software/linux/instantclient/213000/instantclient-sdk-linux.x64-21.3.0.0.0.zip && \
    unzip oci-sdk.zip -d src

RUN echo "instantclient,${ORACLE_BUILD_DIR}/src/instantclient_21_3" | pecl install oci8-3.0.1;

RUN cp /usr/lib64/libaio.so.1 /tmp/libaio.so.1
RUN cp ${ORACLE_BUILD_DIR}/src/instantclient_21_3/libclntshcore.so.21.1 /tmp/libclntshcore.so.21.1
RUN cp ${ORACLE_BUILD_DIR}/src/instantclient_21_3/libclntsh.so.21.1 /tmp/libclntsh.so.21.1
RUN cp ${ORACLE_BUILD_DIR}/src/instantclient_21_3/libocci.so.21.1 /tmp/libocci.so.21.1
RUN cp ${ORACLE_BUILD_DIR}/src/instantclient_21_3/libnnz21.so /tmp/libnnz21.so
RUN cp ${ORACLE_BUILD_DIR}/src/instantclient_21_3/libociicus.so /tmp/libociicus.so
RUN cp `php-config --extension-dir`/oci8.so /tmp/oci8.so
RUN echo 'extension=/opt/bref-extra/oci8.so' > /tmp/ext.ini

# Build the final image from the scratch image that contain files you want to export
FROM scratch

COPY --from=ext /tmp/libaio.so.1 /opt/lib/libaio.so.1
COPY --from=ext /tmp/libclntshcore.so.21.1 /opt/lib/libclntshcore.so.21.1
COPY --from=ext /tmp/libclntsh.so.21.1 /opt/lib/libclntsh.so.21.1
COPY --from=ext /tmp/libocci.so.21.1 /opt/lib/libocci.so.21.1
COPY --from=ext /tmp/libnnz21.so /opt/lib/libnnz21.so
COPY --from=ext /tmp/libociicus.so /opt/lib/libociicus.so
COPY --from=ext /tmp/oci8.so /opt/bref-extra/oci8.so
COPY --from=ext /tmp/ext.ini /opt/bref/etc/php/conf.d/ext-oci8.ini
