ARG PHP_VERSION
FROM bref/build-php-$PHP_VERSION:1.7.4 AS ext

ENV LD_LIBRARY_PATH=/usr/lib:/usr/lib64:$LD_LIBRARY_PATH
RUN yum -y install amazon-linux-extras
RUN amazon-linux-extras install epel -y
RUN yum install -y mpdecimal-devel

RUN pecl install decimal
RUN cp `php-config --extension-dir`/decimal.so /tmp/decimal.so
RUN echo 'extension=/opt/bref-extra/decimal.so' > /tmp/ext.ini

# Build the final image with just the files we need
FROM scratch

# Copy things we installed to the final image
COPY --from=ext /usr/lib64/libmpdec.so.2.4.2 /opt/bref/lib64/libmpdec.so.2.4.2
COPY --from=ext /usr/lib64/libmpdec.so.2 /opt/bref/lib64/libmpdec.so.2
COPY --from=ext /usr/lib64/libmpdec.so /opt/bref/lib64/libmpdec.so

COPY --from=ext /tmp/decimal.so /opt/bref-extra/decimal.so
COPY --from=ext /tmp/ext.ini /opt/bref/etc/php/conf.d/ext-decimal.ini
