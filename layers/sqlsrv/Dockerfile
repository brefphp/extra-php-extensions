ARG PHP_VERSION
ARG BREF_VERSION
FROM bref/build-php-$PHP_VERSION:$BREF_VERSION AS ext
ARG PHP_VERSION

ENV LD_LIBRARY_PATH=/usr/lib:/usr/lib64:$LD_LIBRARY_PATH

# Workaround for openssl-snapsafe-libs conflict in AL2 Lambda base images.
# See: https://github.com/aws/aws-lambda-base-images/issues/245#issuecomment-2714953498
RUN printf '%s\n' \
    '[amzn2extra-openssl-snapsafe]' \
    'name=Amazon Extras repo for openssl-snapsafe' \
    'enabled=1' \
    'mirrorlist=$awsproto://$amazonlinux.$awsregion.$awsdomain/2/extras/openssl-snapsafe/latest/$basearch/mirror.list' \
    'gpgcheck=1' \
    'gpgkey=file:///etc/pki/rpm-gpg/RPM-GPG-KEY-amazon-linux-2' \
    'priority=10' \
    'skip_if_unavailable=1' \
    'report_instanceid=yes' \
    > /etc/yum.repos.d/amzn2-extras-snapsafe.repo
RUN yum upgrade -y openssl-snapsafe-libs

RUN yum -y install unixODBC-devel-2.3.1-14.amzn2.x86_64

RUN curl https://packages.microsoft.com/config/rhel/7/prod.repo > /etc/yum.repos.d/mssql-release.repo
RUN ACCEPT_EULA=Y yum -y install msodbcsql17-17.10.6.1-1.x86_64

RUN if [ "$PHP_VERSION" = "80" ]; \
    then SQLSRV_VERSION=5.11.1 ; \
    elif [ "$PHP_VERSION" = "81" ] || [ "$PHP_VERSION" = "82" ]; \
    then SQLSRV_VERSION=5.12.0 ; \
    else SQLSRV_VERSION=5.13.0 ; \
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
COPY --from=ext /tmp/sqlsrv.so /opt/bref/extensions/sqlsrv.so
COPY --from=ext /tmp/pdo_sqlsrv.so /opt/bref/extensions/pdo_sqlsrv.so
COPY --from=ext /tmp/ext.ini /opt/bref/etc/php/conf.d/ext-sqlsrv.ini
COPY --from=ext /tmp/extension-libs /opt/lib

COPY --from=ext /etc/odbcinst.ini /opt/microsoft/conf/odbcinst.ini
