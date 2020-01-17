ARG PHP_VERSION
FROM bref/build-php-$PHP_VERSION AS ext

ENV LD_LIBRARY_PATH=/usr/lib:/usr/lib64:$LD_LIBRARY_PATH

# Install packages
RUN yum install -y php-odbc

RUN cp /usr/lib64/php/modules/odbc.so /tmp/odbc.so

# Build the final image from the lambci image that is close to the production environment
FROM lambci/lambda:provided

# Copy things we installed to the final image
COPY --from=ext /tmp/odbc.so /opt/bref/lib64/odbc.so

