ARG PHP_VERSION
ARG BREF_VERSION
FROM bref/build-php-$PHP_VERSION:$BREF_VERSION AS ext

RUN pecl install igbinary
RUN cp `php-config --extension-dir`/igbinary.so /tmp/igbinary.so
RUN echo 'extension=igbinary.so' > /tmp/ext.ini

# Build the final image with just the files we need
FROM scratch

# Copy things we installed to the final image
COPY --from=ext /tmp/igbinary.so /opt/bref/extensions/igbinary.so
COPY --from=ext /tmp/ext.ini /opt/bref/etc/php/conf.d/ext-igbinary.ini
