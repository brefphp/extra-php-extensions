ARG PHP_VERSION
FROM bref/build-php-$PHP_VERSION:1.0.0 AS ext

ENV LD_LIBRARY_PATH=/usr/lib:/usr/lib64:$LD_LIBRARY_PATH
RUN yum install -y unixODBC unixODBC-devel

# strongly inspired by https://github.com/mlocati/docker-php-extension-installer/blob/master/install-php-extensions
WORKDIR /tmp/build/php/ext/odbc
RUN phpize \
  && sed -ri 's@^ *test +"\$PHP_.*" *= *"no" *&& *PHP_.*=yes *$@#&@g' configure \
  && ./configure --prefix=${INSTALL_DIR} --with-unixODBC=shared,/usr

RUN make -j $(nproc) install
RUN cp /opt/bref/lib/php/extensions/no-debug-non-zts-*/odbc.so /tmp/odbc.so
RUN echo 'extension=/opt/bref-extra/odbc.so' > /tmp/ext.ini

# https://docs.snowflake.com/en/user-guide/odbc-download.html
# https://sfc-repo.snowflakecomputing.com/odbc/linux/2.21.3/index.html
RUN curl https://sfc-repo.snowflakecomputing.com/odbc/linux/2.21.3/snowflake_linux_x8664_odbc-2.21.3.tgz |tar xzv --directory /tmp
# modify the config files for the new final location in /opt/snowflake_odbc
RUN sed -i 's#/path/to/your/#/opt/snowflake_odbc/lib/#g' /tmp/snowflake_odbc/conf/*

# Build the final image from the lambci image that is close to the production environment
FROM scratch

COPY --from=ext /usr/lib64/libodb* /opt/bref/lib64/
COPY --from=ext /usr/lib64/libltdl.so.7.3.0 /opt/bref/lib64/libltdl.so.7
COPY --from=ext /tmp/snowflake_odbc/ /opt/snowflake_odbc/
COPY --from=ext /tmp/odbc.so /opt/bref-extra/
COPY --from=ext /tmp/ext.ini /opt/bref/etc/php/conf.d/ext-odbc.ini
