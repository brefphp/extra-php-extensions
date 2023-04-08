#!/bin/bash

arch=$(arch | sed s/_/-/g)
php_version=$(php-config --version | cut -c -3)

if [[ $arch = "aarch64" ]]; then
    # Download client
    curl -o oci-basic.zip https://download.oracle.com/otn_software/linux/instantclient/191000/instantclient-basiclite-linux.arm64-19.10.0.0.0dbru-2.zip
    unzip oci-basic.zip -d src

    # Download client sdk
    curl -o oci-sdk.zip https://download.oracle.com/otn_software/linux/instantclient/191000/instantclient-sdk-linux.arm64-19.10.0.0.0dbru.zip
    unzip oci-sdk.zip -d src

    # Install
    if [ "$php_version" = "8.0" ] ; then \
        echo "instantclient,${ORACLE_BUILD_DIR}/src/instantclient_19_10" | pecl install oci8-3.0.1; \
    else \
        echo "instantclient,${ORACLE_BUILD_DIR}/src/instantclient_19_10" | pecl install oci8; \
    fi
else
    # Download client
    curl -o oci-basic.zip https://download.oracle.com/otn_software/linux/instantclient/215000/instantclient-basiclite-linux.x64-21.5.0.0.0dbru.zip
    unzip oci-basic.zip -d src

    # Download client sdk
    curl -o oci-sdk.zip https://download.oracle.com/otn_software/linux/instantclient/215000/instantclient-sdk-linux.x64-21.5.0.0.0dbru.zip
    unzip oci-sdk.zip -d src

    # Install
    if [ "$php_version" = "8.0" ] ; then \
        echo "instantclient,${ORACLE_BUILD_DIR}/src/instantclient_21_5" | pecl install oci8-3.0.1; \
    else \
        echo "instantclient,${ORACLE_BUILD_DIR}/src/instantclient_21_5" | pecl install oci8; \
    fi
fi
