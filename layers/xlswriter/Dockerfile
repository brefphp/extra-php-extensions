ARG PHP_VERSION
ARG BREF_VERSION
FROM bref/build-php-$PHP_VERSION:$BREF_VERSION AS ext

RUN set -xe; \
  printf "yes\n" | pecl install xlswriter; \
  cp `php-config --extension-dir`/xlswriter.so /tmp/xlswriter.so; \
  echo 'extension=xlswriter.so' > /tmp/ext.ini

# Build the final image with just the files we need
FROM scratch

# Copy things we installed to the final image
COPY --from=ext /tmp/xlswriter.so /opt/bref/extensions/xlswriter.so
COPY --from=ext /tmp/ext.ini /opt/bref/etc/php/conf.d/ext-xlswriter.ini
