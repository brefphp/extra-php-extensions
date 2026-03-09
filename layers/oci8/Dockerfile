ARG PHP_VERSION
ARG BREF_VERSION
ARG OL_VERSION=8
ARG IC_VERSION=21
# Oracle Instant Client image
FROM public.ecr.aws/docker/library/oraclelinux:$OL_VERSION AS oracle_base
ARG OL_VERSION
RUN dnf -y install oracle-instantclient-release-el${OL_VERSION}

FROM oracle_base AS oracle_devel
RUN dnf -y install oracle-instantclient-devel && \
        mkdir -p /opt/oracle && \
        cp -r /usr/lib/oracle/*/client64/lib/* /opt/oracle/ && \
        cp -r /usr/include/oracle/*/client64/* /opt/oracle/ 

FROM oracle_base AS oracle_client
RUN dnf -y install oracle-instantclient-basiclite && \
        mkdir -p /opt/oracle && \
        cp -r /usr/lib/oracle/*/client64/lib/* /opt/oracle/

# Redeclare for scope
ARG PHP_VERSION
ARG BREF_VERSION
FROM bref/build-php-$PHP_VERSION:$BREF_VERSION AS ext
ARG IC_VERSION
ENV ORACLE_DEVEL_DIR=/opt/oracle-devel
ENV ORACLE_CLIENT_DIR=/opt/oracle-client
ENV LD_LIBRARY_PATH=/usr/lib:/usr/lib64:$ORACLE_CLIENT_DIR:$ORACLE_DEVEL_DIR:$LD_LIBRARY_PATH

COPY --from=oracle_devel /usr/lib64/libaio.so.1 /usr/lib64/libaio.so.1
COPY --from=oracle_devel /opt/oracle $ORACLE_DEVEL_DIR
COPY --from=oracle_client /opt/oracle $ORACLE_CLIENT_DIR

RUN echo "instantclient,${ORACLE_DEVEL_DIR}" | pecl install oci8

RUN cp /usr/lib64/libaio.so.1 /tmp/libaio.so.1 && \
    cp ${ORACLE_CLIENT_DIR}/libclntshcore.so.${IC_VERSION}.1 /tmp && \
    cp ${ORACLE_CLIENT_DIR}/libclntsh.so.${IC_VERSION}.1 /tmp && \
    cp ${ORACLE_CLIENT_DIR}/libnnz${IC_VERSION}.so /tmp && \
    cp ${ORACLE_CLIENT_DIR}/libocci.so.${IC_VERSION}.1 /tmp && \
    cp ${ORACLE_CLIENT_DIR}/libociicus.so /tmp && \
    cp `php-config --extension-dir`/oci8.so /tmp/oci8.so && \
    echo 'extension=oci8.so' > /tmp/ext.ini

RUN php /bref/lib-copy/copy-dependencies.php /tmp/oci8.so /tmp/extension-libs && \
    cp /tmp/libocci.so.${IC_VERSION}.1 /tmp/extension-libs && \
    cp /tmp/libociicus.so /tmp/extension-libs

# Build the final image from the scratch image that contain files you want to export
FROM scratch

COPY --from=ext /tmp/oci8.so /opt/bref/extensions/oci8.so
COPY --from=ext /tmp/ext.ini /opt/bref/etc/php/conf.d/ext-oci8.ini
COPY --from=ext /tmp/extension-libs /opt/lib