# How to build new layer

To create your own extension layer, compile the extension and any required libraries on Bref environment with Docker.
It picks up pgsql extension as an example, so please replace the name appropriately.

First create new extension directory in `layers/` and move there.

```bash
$ mkdir layers/pgsql && cd $_
```

Create `Dockerfile` such as follows.

```Dockerfile
ARG PHP_VERSION
FROM bref/build-php-$PHP_VERSION AS ext

#
# Add commands to build PHP extensions here.
#

# Build the final image from the lambci image that is close to the production environment
FROM lambci/lambda:provided

#
# Add commands to copy files that requried for final image.
#
```

Then you need to add two part of Dockerfile, the following snippets are examples of build and file copy parts respectively.

```Dockerfile

WORKDIR ${PHP_BUILD_DIR}/ext/pgsql
RUN phpize
RUN ./configure --with-pgsql=${INSTALL_DIR}
RUN make -j `nproc` && make install

RUN cp `php-config --extension-dir`/pgsql.so /tmp/pgsql.so
```
Some env variables are set and can be used in this environment. `PHP_BUILD_DIR` is `/tmp/build/php`, `INSTALL_DIR` is `/opt/bref`.

```Dockerfile
COPY --from=ext /tmp/pgsql.so /opt/bref-extra/pgsql.so
```

Extension files that need to be installed should be placed `/opt` directory in the final image.

It might be good to build extension step-by-step and create Dockerfile from command history instead of immediately building from Dockerfile.

```bash
$ docker run -it bref/build-php-$PHP_VERSION /bin/bash  # Run build environment with ”-it” option and build the extension step by step.
```

Once you have created Dockerfile, make sure the build suceeds.

```bash
$ make docker-images
```

If the build goes through, generate zip files of the layers in `export/` directory.

```bash
$ make layer
```

Register the zip file generated above to AWS as Lambda Layer. It also able to add from AWS console.

```bash
$ aws lambda publish-layer-version --layer-name pgsql-php-73 --zip-file fileb://./export/layer-pgsql-php-73.zip
```

# How to test layers

Create a Bref App for testing. The usage of `bref` and `serverless`, please refer [here](https://bref.sh/docs/installation.html).

```bash
$ mkdir pgsql-test-app && $_
$ composer init
## Everything OK for default.
$ composer require bref/bref
$ vendor/bin/bref init
 What kind of lambda do you want to create? (you will be able to add more functions later by editing `serverless.yml`) [PHP function]:
  [0] PHP function
  [1] HTTP application
  [2] Console application
 > 1                          
## Select suitable for check your extension.
```

In `serverless.yml`, edit `region` and add layer ARN which you added earlier.

```diff
service: app

provider:
    name: aws
-    region: us-east-1
+    region: <YOUR AWS REGION>
    runtime: provided

plugins:
    - ./vendor/bref/bref

functions:
    api:
        handler: index.php
        description: ''
        timeout: 28 # in seconds (API Gateway has a timeout of 29 seconds)
        layers:
            - ${bref:layer.php-73-fpm}
+            - arn:aws:lambda:<YOUR AWS REGION>:<YOUR AWS ID>:layer:pgsql-php-73:3

        events:
            -   http: 'ANY /'
            -   http: 'ANY /{proxy+}'
```

Update `index.php` to be allowed you to check the extension.

```diff
<?php

- echo "Hello World";
+ #
+ # Add some check processing
+ #
```

Add a setting to load the extension.

```bash
$ mkdir -p php/conf.d
$ echo "extension=/opt/bref-extra/pgsql.so" > php/conf.d/pgsql.ini
```

Finally deploy and test this function.

```bash
$ serverless deploy
```

# Prepare for contribution

If you'd like to contribute, please confirm that it works with each PHP versions in the way described above, and update the document and CI settings.

Update the table in the `Readme.md` in alphabetical order.

```diff
 ### Available layers

 | Name | Serverless config (php 7.4) | php.ini config |
 | ---- | ----------------------------| -------------- |
 | AMQP | `${bref:extra.amqp-php-74}` | `extension=/opt/bref-extra/amqp.so` |
 | Blackfire | `${bref:extra.blackfire-php-74}` | `extension=/opt/bref-extra/blackfire.so` |
 | GMP | `${bref:extra.gmp-php-74}` | `extension=/opt/bref-extra/gmp.so` |
 | Memcache | `${bref:extra.memcached-php-74}` | `extension=/opt/bref-extra/memcache.so` |
 | Memcached | `${bref:extra.memcached-php-74}` | `extension=/opt/bref-extra/memcached.so` |
+| PostgreSQL | `${bref:extra.pgsql-php-74}` | `extension=/opt/bref-extra/pgsql.so` |
 | Xdebug | `${bref:extra.xdebug-php-74}` | `zend_extension=/opt/bref-extra/xdebug.so` |

 Note that the "Memcached" layer provides both extension for [Memcache](https://pecl.php.net/package/memcache) and [Memcached](https://pecl.php.net/package/memcached).
```

Update `.travis.yml` as follows.

```diff
 env:
   - LAYER=amqp PHP="72 73 74"
   - LAYER=blackfire PHP="72 73 74"
   - LAYER=gmp PHP="72 73 74"
   - LAYER=memcached PHP="72 73 74"
+  - LAYER=pgsql PHP="72 73 74"
   - LAYER=xdebug PHP="72 73 74"
```

Once you have done above please submit the PR.
