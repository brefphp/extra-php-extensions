ARG PHP_VERSION
ARG BREF_VERSION
FROM bref/build-php-$PHP_VERSION:$BREF_VERSION AS ext

# Build the New Relic Agent - install silently
ARG NEWRELIC_VERSION=10.11.0.3
RUN \
  curl -L https://download.newrelic.com/php_agent/archive/${NEWRELIC_VERSION}/newrelic-php5-${NEWRELIC_VERSION}-linux.tar.gz | tar -C /tmp -zx && \
  export NR_INSTALL_USE_CP_NOT_LN=1 && \
  export NR_INSTALL_SILENT=1 && \
  /tmp/newrelic-php5-*/newrelic-install install && \
  rm -rf /tmp/newrelic-php5-* /tmp/nrinstall*

# newrelic.so will be installed to `php-config --extension-dir`/newrelic.so
# In this core image newrelic.ini will be placed but not exist in the scatch layer
# ${INSTALL_DIR}/etc/php/php.ini /tmp/php.ini - php.ini will contain the needed config and extension ref

# Copy the newrelic.so and php.ini file to the tmp folder

RUN cp `php -i | grep extension_dir | sed 's/^.*=> \(.*\)$/\1/' | head -1`/newrelic.so /tmp/newrelic.so
RUN echo 'extension=newrelic.so' > /tmp/newrelic.ini

# Build the final image from the scratch image that contain files you want to export
FROM scratch

# Copy the two key files to the correct location for the empty layer.
# newrelic.so should become a layered extension.
COPY --from=ext /tmp/newrelic.so /opt/bref/extensions/newrelic.so
COPY --from=ext /tmp/newrelic.ini /opt/bref/etc/php/conf.d/ext-newrelic.ini
