ARG PHP_VERSION
ARG BREF_VERSION
FROM bref/build-php-$PHP_VERSION:$BREF_VERSION AS ext
ARG PHP_VERSION

ENV LD_LIBRARY_PATH=/usr/lib:/usr/lib64:$LD_LIBRARY_PATH

RUN dnf -y install unixODBC-devel-2.3.9-3.amzn2023.0.3.x86_64

RUN curl -fsSL https://packages.microsoft.com/config/rhel/9/prod.repo > /etc/yum.repos.d/mssql-release.repo
RUN ACCEPT_EULA=Y dnf -y install msodbcsql18-18.6.1.1-1.x86_64

RUN if [ "$PHP_VERSION" = "84" ]; \
    then SQLSRV_VERSION=5.13.0beta1 ; \
    else SQLSRV_VERSION=5.12.0 ; \
    fi ; \
    pecl install sqlsrv-${SQLSRV_VERSION} && \
    pecl install pdo_sqlsrv-${SQLSRV_VERSION}

RUN cp `php-config --extension-dir`/sqlsrv.so /tmp/sqlsrv.so
RUN cp `php-config --extension-dir`/pdo_sqlsrv.so /tmp/pdo_sqlsrv.so

RUN echo $'extension=sqlsrv.so\n\
  extension=pdo_sqlsrv.so' > /tmp/ext.ini

RUN php /bref/lib-copy/copy-dependencies.php /tmp/sqlsrv.so /tmp/extension-libs
RUN php /bref/lib-copy/copy-dependencies.php /tmp/pdo_sqlsrv.so /tmp/extension-libs


FROM scratch

COPY --from=ext /opt/microsoft /opt/microsoft
COPY --from=ext /etc/odbcinst.ini /opt/microsoft/conf/odbcinst.ini
COPY --from=ext /tmp/sqlsrv.so /opt/bref/extensions/sqlsrv.so
COPY --from=ext /tmp/pdo_sqlsrv.so /opt/bref/extensions/pdo_sqlsrv.so
COPY --from=ext /tmp/ext.ini /opt/bref/etc/php/conf.d/ext-sqlsrv.ini
COPY --from=ext /tmp/extension-libs /opt/lib
