ARG PHP_VERSION
FROM bref/build-php-$PHP_VERSION:1.3.3 AS ext

RUN set -xe; \
  if [ $PHP_VERSION != "81" ] ; \
  then \
    pecl install xdebug; \
  else \
    cd /tmp; \
    git clone --depth=1 https://github.com/xdebug/xdebug.git; \
    cd xdebug; \
    phpize; \
    ./configure; \
    make; \
    make install; \
  fi; \
  cp `php-config --extension-dir`/xdebug.so /tmp/xdebug.so; \
  echo 'zend_extension=/opt/bref-extra/xdebug.so' > /tmp/ext.ini

# Build the final image with just the files we need
FROM scratch

# Copy things we installed to the final image
COPY --from=ext /tmp/xdebug.so /opt/bref-extra/xdebug.so
COPY --from=ext /tmp/ext.ini /opt/bref/etc/php/conf.d/ext-xdebug.ini
