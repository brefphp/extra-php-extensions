ARG PHP_VERSION
FROM bref/build-php-$PHP_VERSION:1.1.4 AS ext

ENV LD_LIBRARY_PATH=/usr/lib:/usr/lib64:$LD_LIBRARY_PATH

RUN curl https://packages.microsoft.com/config/rhel/7/prod.repo > /etc/yum.repos.d/mssql-release.repo
RUN ACCEPT_EULA=Y yum -y install msodbcsql17-17.7.1.1-1.x86_64 mssql-tools-17.7.1.1-1.x86_64
RUN ACCEPT_EULA=Y yum -y install unixODBC-devel-2.3.7-1.rh.x86_64

RUN pecl install -f sqlsrv-5.9.0
RUN pecl install -f pdo_sqlsrv-5.9.0

RUN cp /opt/bref/lib/php/extensions/no-debug-non-zts-*/sqlsrv.so /tmp/sqlsrv.so
RUN cp /opt/bref/lib/php/extensions/no-debug-non-zts-*/pdo_sqlsrv.so /tmp/pdo_sqlsrv.so

RUN echo $'extension=/opt/bref-extra/sqlsrv.so\n\
extension=/opt/bref-extra/pdo_sqlsrv.so' > /tmp/ext.ini

RUN echo $'[ODBC Driver 17 for SQL Server]\
Driver      = ODBC Driver 17 for SQL Server\n\
Description = My ODBC Driver 17 for SQL Server\n\
Trace       = No'  > /tmp/odbc.ini
RUN echo $'[ODBC Driver 17 for SQL Server]\n\
Description = Microsoft ODBC Driver 17 for SQL Server\n\
Driver      = /opt/bref/lib64/libmsodbcsql-17.so\n\
UsageCount  = 1'  > /tmp/odbcinst.ini

FROM scratch

COPY --from=ext /opt/microsoft* /opt/microsoft

COPY --from=ext /usr/lib64/libe2p* /opt/bref/lib64/
COPY --from=ext /usr/lib64/libext2fs* /opt/bref/lib64/
COPY --from=ext /usr/lib64/libmsodbcsql-17* /opt/bref/lib64/
COPY --from=ext /usr/lib64/libodbc* /opt/bref/lib64/
COPY --from=ext /usr/lib64/libss* /opt/bref/lib64/

COPY --from=ext /tmp/sqlsrv.so /opt/bref-extra/sqlsrv.so
COPY --from=ext /tmp/pdo_sqlsrv.so /opt/bref-extra/pdo_sqlsrv.so
COPY --from=ext /tmp/ext.ini /opt/bref/etc/php/conf.d/ext-sqlsrv.ini

COPY --from=ext /tmp/odbc.ini /opt/microsoft/conf/odbc.ini
COPY --from=ext /tmp/odbcinst.ini /opt/microsoft/conf/odbcinst.ini
