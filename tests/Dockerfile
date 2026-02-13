ARG PHP_VERSION
ARG TARGET_IMAGE

FROM bref/$TARGET_IMAGE AS ext
FROM bref/php-$PHP_VERSION:3

COPY --from=ext /opt /opt
ENV ODBCSYSINI=/opt/microsoft/conf/
