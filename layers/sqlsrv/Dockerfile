ARG PHP_VERSION
ARG BREF_VERSION
FROM bref/build-php-$PHP_VERSION:$BREF_VERSION AS ext
ARG PHP_VERSION

ENV LD_LIBRARY_PATH=/usr/lib:/usr/lib64:$LD_LIBRARY_PATH

RUN yum -y install unixODBC-devel-2.3.1-14.amzn2.x86_64

RUN curl https://packages.microsoft.com/config/rhel/7/prod.repo > /etc/yum.repos.d/mssql-release.repo
RUN ACCEPT_EULA=Y yum -y install msodbcsql17-17.10.1.1-1.x86_64

RUN pecl install sqlsrv-5.11.1 && \
  pecl install pdo_sqlsrv-5.11.1

RUN cp `php-config --extension-dir`/sqlsrv.so /tmp/sqlsrv.so
RUN cp `php-config --extension-dir`/pdo_sqlsrv.so /tmp/pdo_sqlsrv.so

RUN echo $'extension=sqlsrv.so\n\
  extension=pdo_sqlsrv.so' > /tmp/ext.ini

RUN php /bref/lib-copy/copy-dependencies.php /tmp/sqlsrv.so /tmp/extension-libs
RUN php /bref/lib-copy/copy-dependencies.php /tmp/pdo_sqlsrv.so /tmp/extension-libs


FROM scratch

COPY --from=ext /opt/microsoft /opt/microsoft
COPY --from=ext /tmp/sqlsrv.so /opt/bref/extensions/sqlsrv.so
COPY --from=ext /tmp/pdo_sqlsrv.so /opt/bref/extensions/pdo_sqlsrv.so
COPY --from=ext /tmp/ext.ini /opt/bref/etc/php/conf.d/ext-sqlsrv.ini
COPY --from=ext /tmp/extension-libs /opt/lib

COPY --from=ext /etc/odbcinst.ini /opt/microsoft/conf/odbcinst.ini
