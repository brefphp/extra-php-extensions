# syntax = docker/dockerfile:1.4
ARG PHP_VERSION
ARG BREF_VERSION
FROM bref/build-php-$PHP_VERSION:$BREF_VERSION AS ext

RUN <<eot bash -xe
  pecl install xdebug
  cp `php-config --extension-dir`/xdebug.so /tmp/xdebug.so
  echo 'zend_extension=xdebug.so' > /tmp/ext.ini
eot

# Build the final image with just the files we need
FROM scratch

# Copy things we installed to the final image
COPY --from=ext /tmp/xdebug.so /opt/bref/extensions/xdebug.so
COPY --from=ext /tmp/ext.ini /opt/bref/etc/php/conf.d/ext-xdebug.ini
