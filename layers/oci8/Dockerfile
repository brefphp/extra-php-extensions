ARG PHP_VERSION
ARG BREF_VERSION
FROM bref/build-php-$PHP_VERSION:$BREF_VERSION AS ext
ARG PHP_VERSION

# Specify library path
ENV LD_LIBRARY_PATH=/usr/lib:/usr/lib64:$LD_LIBRARY_PATH
ENV ORACLE_BUILD_DIR=${BUILD_DIR}/oracle

# Install libaio
RUN yum install -y libaio

RUN mkdir -p ${ORACLE_BUILD_DIR}; \
    cd ${ORACLE_BUILD_DIR}; \
    curl -o oci-basic.zip https://download.oracle.com/otn_software/linux/instantclient/2110000/instantclient-basiclite-linux.x64-21.10.0.0.0dbru.zip && \
    unzip oci-basic.zip -d src && \
    curl -o oci-sdk.zip https://download.oracle.com/otn_software/linux/instantclient/2110000/instantclient-sdk-linux.x64-21.10.0.0.0dbru.zip && \
    unzip oci-sdk.zip -d src

RUN if [ "$PHP_VERSION" = "80" ] ; then \
    echo "instantclient,${ORACLE_BUILD_DIR}/src/instantclient_21_10" | pecl install oci8-3.0.1; \
    elif [ "$PHP_VERSION" = "81" ] ; then \
    echo "instantclient,${ORACLE_BUILD_DIR}/src/instantclient_21_10" | pecl install oci8-3.2.1; \
    else \
    echo "instantclient,${ORACLE_BUILD_DIR}/src/instantclient_21_10" | pecl install oci8; \
    fi

RUN cp /usr/lib64/libaio.so.1 /tmp/libaio.so.1
RUN cp ${ORACLE_BUILD_DIR}/src/instantclient_21_10/libclntshcore.so.21.1 /tmp/libclntshcore.so.21.1
RUN cp ${ORACLE_BUILD_DIR}/src/instantclient_21_10/libclntsh.so.21.1 /tmp/libclntsh.so.21.1
RUN cp ${ORACLE_BUILD_DIR}/src/instantclient_21_10/libocci.so.21.1 /tmp/libocci.so.21.1
RUN cp ${ORACLE_BUILD_DIR}/src/instantclient_21_10/libnnz21.so /tmp/libnnz21.so
RUN cp ${ORACLE_BUILD_DIR}/src/instantclient_21_10/libociicus.so /tmp/libociicus.so
RUN cp `php-config --extension-dir`/oci8.so /tmp/oci8.so
RUN echo 'extension=oci8.so' > /tmp/ext.ini

RUN php /bref/lib-copy/copy-dependencies.php /tmp/oci8.so /tmp/extension-libs
# Missing these two
RUN cp /tmp/libocci.so.21.1 /tmp/extension-libs
RUN cp /tmp/libociicus.so /tmp/extension-libs

# Build the final image from the scratch image that contain files you want to export
FROM scratch

COPY --from=ext /tmp/oci8.so /opt/bref/extensions/oci8.so
COPY --from=ext /tmp/ext.ini /opt/bref/etc/php/conf.d/ext-oci8.ini
COPY --from=ext /tmp/extension-libs /opt/lib
