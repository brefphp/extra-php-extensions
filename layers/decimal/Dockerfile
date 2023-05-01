ARG PHP_VERSION
ARG BREF_VERSION
FROM bref/build-php-$PHP_VERSION:$BREF_VERSION AS ext

ENV LD_LIBRARY_PATH=/usr/lib:/usr/lib64:$LD_LIBRARY_PATH
RUN yum -y install amazon-linux-extras
RUN amazon-linux-extras install epel -y
RUN yum install -y mpdecimal-devel

RUN pecl install decimal
RUN cp `php-config --extension-dir`/decimal.so /tmp/decimal.so
RUN echo 'extension=decimal.so' > /tmp/ext.ini

RUN php /bref/lib-copy/copy-dependencies.php /tmp/decimal.so /tmp/extension-libs


# Build the final image with just the files we need
FROM scratch

# Copy things we installed to the final image
COPY --from=ext /tmp/decimal.so /opt/bref/extensions/decimal.so
COPY --from=ext /tmp/ext.ini /opt/bref/etc/php/conf.d/ext-decimal.ini
COPY --from=ext /tmp/extension-libs /opt/lib
