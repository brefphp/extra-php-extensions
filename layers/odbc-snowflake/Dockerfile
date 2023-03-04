ARG PHP_VERSION
ARG BREF_VERSION
FROM bref/build-php-$PHP_VERSION:$BREF_VERSION AS ext

ENV LD_LIBRARY_PATH=/usr/lib:/usr/lib64:$LD_LIBRARY_PATH
RUN yum install -y unixODBC unixODBC-devel

# strongly inspired by https://github.com/mlocati/docker-php-extension-installer/blob/master/install-php-extensions
WORKDIR ${PHP_BUILD_DIR}/ext/odbc
RUN phpize \
  && sed -ri 's@^ *test +"\$PHP_.*" *= *"no" *&& *PHP_.*=yes *$@#&@g' configure \
  && ./configure --prefix=${INSTALL_DIR} --with-unixODBC=shared,/usr

RUN make -j $(nproc) install
RUN cp `php-config --extension-dir`/odbc.so /tmp/odbc.so
RUN echo 'extension=odbc.so' > /tmp/ext.ini

# https://docs.snowflake.com/en/user-guide/odbc-download.html
# https://sfc-repo.snowflakecomputing.com/odbc/linux/2.25.0/index.html
# also: strip more than 100 mb of debug symbols
RUN curl https://sfc-repo.snowflakecomputing.com/odbc/linux/2.25.0/snowflake_linux_x8664_odbc-2.25.0.tgz |tar xzv --directory /tmp \
  && strip -g /tmp/snowflake_odbc/lib/libSnowflake.so
# modify the config files for the new final location in /opt/snowflake_odbc
RUN sed -i 's#/path/to/your/#/opt/snowflake_odbc/lib/#g' /tmp/snowflake_odbc/conf/*

RUN php /bref/lib-copy/copy-dependencies.php /tmp/odbc.so /tmp/extension-libs


# Build the final image with just the files we need
FROM scratch

COPY --from=ext /tmp/snowflake_odbc/ /opt/snowflake_odbc/
COPY --from=ext /tmp/odbc.so /opt/bref/extensions/
COPY --from=ext /tmp/ext.ini /opt/bref/etc/php/conf.d/ext-odbc.ini
COPY --from=ext /tmp/extension-libs /opt/lib
