ARG PHP_VERSION
ARG BREF_VERSION
FROM bref/build-php-$PHP_VERSION:$BREF_VERSION AS ext

# Datastax is providing a PHP Cassandra driver, but this hasn't been updated for a while and doesn't work with PHP 7.2 or higher
# Datastax indicated there will be updates in the near future, so check https://docs.datastax.com/en/developer/php-driver/ if you want to avoid building your own :-)

ENV LIBUV_VERSION 1.35.0
ENV CCP_DRIVER_VERSION 2.15.2

# Fix library path
ENV LD_LIBRARY_PATH=/usr/lib:/usr/lib64:$LD_LIBRARY_PATH

WORKDIR /tmp

RUN yum install -y automake cmake gcc gcc-c++ git libtool openssl-devel wget gmp gmp-devel boost php-devel pcre-devel
RUN LD_LIBRARY_PATH=/lib64:/lib yum install -y libuv-devel

RUN yum install -y https://downloads.datastax.com/cpp-driver/centos/7/cassandra/v$CCP_DRIVER_VERSION/cassandra-cpp-driver-$CCP_DRIVER_VERSION-1.el7.x86_64.rpm
RUN yum install -y https://downloads.datastax.com/cpp-driver/centos/7/cassandra/v$CCP_DRIVER_VERSION/cassandra-cpp-driver-devel-$CCP_DRIVER_VERSION-1.el7.x86_64.rpm

RUN cd /tmp && \
    git clone https://github.com/datastax/php-driver.git && \
    cd php-driver && \
    pecl install ext/package.xml

RUN cp `php-config --extension-dir`/cassandra.so /tmp/cassandra.so
RUN echo 'extension=cassandra.so' > /tmp/ext.ini

RUN php /bref/lib-copy/copy-dependencies.php /tmp/cassandra.so /tmp/extension-libs


# Build the final image with just the files we need
FROM scratch

# php extension binary
COPY --from=ext /tmp/cassandra.so /opt/bref/extensions/cassandra.so
COPY --from=ext /tmp/ext.ini /opt/bref/etc/php/conf.d/ext-cassandra.ini
COPY --from=ext /tmp/extension-libs /opt/lib
