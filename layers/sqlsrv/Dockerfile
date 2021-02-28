ARG PHP_VERSION
FROM bref/build-php-$PHP_VERSION:1.1.4 AS ext

ENV LD_LIBRARY_PATH=/usr/lib:/usr/lib64:$LD_LIBRARY_PATH

RUN yum -y install unixODBC-devel-2.3.1-14.amzn2.x86_64

RUN curl https://packages.microsoft.com/config/rhel/7/prod.repo > /etc/yum.repos.d/mssql-release.repo
RUN ACCEPT_EULA=Y yum -y install msodbcsql17-17.7.1.1-1.x86_64

RUN pecl install sqlsrv-5.9.0
RUN pecl install pdo_sqlsrv-5.9.0

RUN cp /opt/bref/lib/php/extensions/no-debug-non-zts-*/sqlsrv.so /tmp/sqlsrv.so
RUN cp /opt/bref/lib/php/extensions/no-debug-non-zts-*/pdo_sqlsrv.so /tmp/pdo_sqlsrv.so

RUN echo $'extension=/opt/bref-extra/sqlsrv.so\n\
extension=/opt/bref-extra/pdo_sqlsrv.so' > /tmp/ext.ini

FROM scratch

COPY --from=ext /opt/microsoft* /opt/microsoft

COPY --from=ext /usr/lib64/libe2p* /opt/bref/lib64/
COPY --from=ext /usr/lib64/libesoobS* /opt/bref/lib64/
COPY --from=ext /usr/lib64/libext2fs* /opt/bref/lib64/
COPY --from=ext /usr/lib64/libmimerS* /opt/bref/lib64/
COPY --from=ext /usr/lib64/libmsodbcsql-17* /opt/bref/lib64/
COPY --from=ext /usr/lib64/libnn* /opt/bref/lib64/
COPY --from=ext /usr/lib64/libodbc* /opt/bref/lib64/
COPY --from=ext /usr/lib64/liboplodbcS* /opt/bref/lib64/
COPY --from=ext /usr/lib64/liboraodbcS* /opt/bref/lib64/
COPY --from=ext /usr/lib64/libsapdbS* /opt/bref/lib64/
COPY --from=ext /usr/lib64/libss* /opt/bref/lib64/
COPY --from=ext /usr/lib64/libtdsS* /opt/bref/lib64/
COPY --from=ext /usr/lib64/libtemplate* /opt/bref/lib64/
COPY --from=ext /usr/lib64/libltdl* /opt/bref/lib64/

COPY --from=ext /tmp/sqlsrv.so /opt/bref-extra/sqlsrv.so
COPY --from=ext /tmp/pdo_sqlsrv.so /opt/bref-extra/pdo_sqlsrv.so
COPY --from=ext /tmp/ext.ini /opt/bref/etc/php/conf.d/ext-sqlsrv.ini

COPY --from=ext /etc/odbcinst.ini /opt/microsoft/conf/odbcinst.ini
