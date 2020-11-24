ARG PHP_VERSION
FROM bref/build-php-$PHP_VERSION:1.0.0-beta1 AS ext

ENV LD_LIBRARY_PATH=/usr/lib:/usr/lib64:$LD_LIBRARY_PATH

RUN curl http://mirror.centos.org/centos/7/os/x86_64/Packages/unixODBC-2.3.1-14.el7.x86_64.rpm > /tmp/unixODBC-2.3.1-14.el7.x86_64.rpm
RUN yum -y install /tmp/unixODBC-2.3.1-14.el7.x86_64.rpm
RUN curl http://mirror.centos.org/centos/7/os/x86_64/Packages/unixODBC-devel-2.3.1-14.el7.x86_64.rpm > /tmp/unixODBC-devel-2.3.1-14.el7.x86_64.rpm
RUN yum -y install /tmp/unixODBC-devel-2.3.1-14.el7.x86_64.rpm

RUN curl https://packages.microsoft.com/config/rhel/7/prod.repo > /etc/yum.repos.d/mssql-release.repo
RUN ACCEPT_EULA=Y yum -y install msodbcsql17 mssql-tools

RUN pecl install -f sqlsrv
RUN pecl install -f pdo_sqlsrv

RUN cp /opt/bref/lib/php/extensions/no-debug-non-zts-*/sqlsrv.so /tmp/sqlsrv.so
RUN cp /opt/bref/lib/php/extensions/no-debug-non-zts-*/pdo_sqlsrv.so /tmp/pdo_sqlsrv.so

RUN echo $'[ODBC Driver 17 for SQL Server]\n\
Driver      = ODBC Driver 17 for SQL Server\n\
Description = My ODBC Driver 17 for SQL Server\n\
Trace       = No'  > /tmp/odbc.ini
RUN echo $'[ODBC Driver 17 for SQL Server]\n\
Description = Microsoft ODBC Driver 17 for SQL Server\n\
Driver      = /opt/microsoft/msodbcsql17/lib64/libmsodbcsql-17.6.so.1.1\n\
UsageCount  = 1'  > /tmp/odbcinst.ini

FROM scratch

COPY --from=ext /opt/microsoft* /opt/microsoft

COPY --from=ext /usr/lib64/libe2p* /opt/bref/lib64/
COPY --from=ext /usr/lib64/libesoobS* /opt/bref/lib64/
COPY --from=ext /usr/lib64/libext2fs* /opt/bref/lib64/
COPY --from=ext /usr/lib64/libmimerS* /opt/bref/lib64/
COPY --from=ext /usr/lib64/libnn* /opt/bref/lib64/
COPY --from=ext /usr/lib64/libodbc* /opt/bref/lib64/
COPY --from=ext /usr/lib64/liboplodbcS* /opt/bref/lib64/
COPY --from=ext /usr/lib64/liboraodbcS* /opt/bref/lib64/
COPY --from=ext /usr/lib64/libsapdbS* /opt/bref/lib64/
COPY --from=ext /usr/lib64/libss* /opt/bref/lib64/
COPY --from=ext /usr/lib64/libtdsS* /opt/bref/lib64/
COPY --from=ext /usr/lib64/libtemplate* /opt/bref/lib64/

COPY --from=ext /tmp/sqlsrv.so /opt/bref-extra/sqlsrv.so
COPY --from=ext /tmp/pdo_sqlsrv.so /opt/bref-extra/pdo_sqlsrv.so

COPY --from=ext /tmp/odbc.ini /opt/microsoft/conf/odbc.ini
COPY --from=ext /tmp/odbcinst.ini /opt/microsoft/conf/odbcinst.ini
