ARG PHP_VERSION
FROM bref/build-php-$PHP_VERSION:1.0.0 AS ext

RUN pecl install igbinary
RUN cp `php-config --extension-dir`/igbinary.so /tmp/igbinary.so
RUN echo 'extension=/opt/bref-extra/igbinary.so' > /tmp/ext.ini

# Build the final image from the lambci image that is close to the production environment
FROM scratch

# Copy things we installed to the final image
COPY --from=ext /tmp/igbinary.so /opt/bref-extra/igbinary.so
COPY --from=ext /tmp/ext.ini /opt/bref/etc/php/conf.d/ext-igbinary.ini
