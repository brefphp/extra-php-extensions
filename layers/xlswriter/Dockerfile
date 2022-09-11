ARG PHP_VERSION
FROM bref/build-php-$PHP_VERSION:1.3.3 AS ext

RUN set -xe; \
  printf "yes\n" | pecl install xlswriter; \
  cp `php-config --extension-dir`/xlswriter.so /tmp/xlswriter.so; \
  echo 'extension=/opt/bref-extra/xlswriter.so' > /tmp/ext.ini

# Build the final image with just the files we need
FROM scratch

# Copy things we installed to the final image
COPY --from=ext /tmp/xlswriter.so /opt/bref-extra/xlswriter.so
COPY --from=ext /tmp/ext.ini /opt/bref/etc/php/conf.d/ext-xlswriter.ini
