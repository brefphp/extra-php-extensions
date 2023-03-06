# How to build new layer

To create your own extension layer, compile the extension and any required libraries
with Docker. This guide uses pgsql extension as an example.

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

# Build the final image from the scratch image that contain files you want to export
FROM scratch

#
# Add commands to copy files that required for final image.
#
```

The environment variable `PHP_VERSION` is passed from the Makefile as an argument
to docker build. It may have values like: `80`, `81`. A docker image is created
for each `PHP_VERSION`. If the build procedure of your extension differs for each version,
you may use this variable to switch processing in Dockerfile.

There are some other env variables available, like `PHP_BUILD_DIR` or `INSTALL_DIR`.

### Building your extension

Then you need to add two parts of Dockerfile, the following snippets are examples
of build and copy parts respectively.

```Dockerfile

WORKDIR ${PHP_BUILD_DIR}/ext/pgsql
RUN phpize
RUN ./configure --with-pgsql=${INSTALL_DIR}
RUN make -j `nproc` && make install

RUN cp `php-config --extension-dir`/pgsql.so /tmp/pgsql.so
RUN echo 'extension=pgsql.so' > /tmp/ext.ini
RUN php /bref/lib-copy/copy-dependencies.php /tmp/pgsql.so /tmp/extension-libs
```

You may need to:
 - download the source code,
 - install the libraries required for compilation,
 - perform pecl install,
 - etc.

The Dockerfiles for [these](../layers) extensions could be very helpful.

> **Note**
> The `/bref/lib-copy/copy-dependencies.php` script will automatically copy system dependencies (libraries) used by the extension provided as a first argument.

### Copy files

The final extension layer is just a zip archive of files that overlay the PHP layer.
The extension and all related files that need to be installed should be placed `/opt`
directory in the final image.

```Dockerfile
RUN echo 'extension=pgsql.so' > /tmp/ext.ini

FROM scratch

COPY --from=ext /tmp/pgsql.so /opt/bref/extensions/pgsql.so
COPY --from=ext /tmp/ext.ini /opt/bref/etc/php/conf.d/ext-pgsql.ini
COPY --from=ext /tmp/extension-libs /opt/lib
```

### Making a layer

It might be good to build extension step-by-step and create Dockerfile from command
history instead of immediately building from Dockerfile.

```bash
$ docker run -it bref/build-php-$PHP_VERSION /bin/bash  # Run build environment with ”-it” option and build the extension step by step.
```

Once you have created Dockerfile, make sure the build succeeds.

```bash
$ make docker-images
```

If the build goes through, generate zip files of the layers in `export/` directory.

```bash
$ make layers
```

Register the zip file generated above to AWS as Lambda Layer. It also able to add from AWS console.

```bash
$ aws lambda publish-layer-version --layer-name pgsql-php-80 --zip-file fileb://./export/layer-pgsql-php-80.zip
```

# Test layers

## Automated tests

Create a file called `test.php` in your later directory (`layers/pgsql/test.php`).
That PHP script should check if your extension is properly loaded. If something is
wrote, the script must exit with code 1.

## How to test layers in a real application

Create a Bref App for testing. The usage of `bref` and `serverless`, please refer [here](https://bref.sh/docs/installation.html).

```bash
$ mkdir pgsql-test-app && $_
$ composer init
## Everything OK for default.
$ composer require bref/bref
$ vendor/bin/bref init
 What kind of lambda do you want to create? (you will be able to add more functions later by editing `serverless.yml`) [PHP function]:
  [0] Web application
  [1] PHP function
 > 0
## Select suitable for check your extension.
```

In `serverless.yml`, edit `region` and add layer ARN which you added earlier.

```diff
service: app

provider:
    name: aws
-    region: us-east-1
+    region: <YOUR AWS REGION>
    runtime: provided.al2

plugins:
    - ./vendor/bref/bref

functions:
    api:
        handler: index.php
        description: ''
        timeout: 28 # in seconds (API Gateway has a timeout of 29 seconds)
        layers:
            - ${bref:layer.php-80-fpm}
+            - arn:aws:lambda:<YOUR AWS REGION>:<YOUR AWS ID>:layer:pgsql-php-80:3

        events:
            -   httpApi: '*'
```

Update `index.php` to check that the extension is automatically loaded and works.

```diff
<?php

- echo "Hello World";
+ #
+ # Check the extension works
+ #
```

Finally, deploy and test this function.

```bash
$ serverless deploy
```

# Prepare for contribution

Contributions to add new extensions are welcomed. When you built new extension, please contribute it by all means.
In order to contribute, you should do a little more work.

* Make sure that it works with each PHP versions in the way described above
* Update the table in the `Readme.md` in alphabetical order.

```diff
 ### Available layers

 | Name | Serverless config (php 8.1) |
 | ---- | ----------------------------|
 | AMQP | `${bref-extra:amqp-php-81}` |
 | Blackfire | `${bref-extra:blackfire-php-81}` |
 | GMP | `${bref-extra:gmp-php-81}` |
 | Memcache | `${bref-extra:memcached-php-81}` |
 | Memcached | `${bref-extra:memcached-php-81}` |
+| PostgreSQL | `${bref-extra:pgsql-php-81}` |
 | Xdebug | `${bref-extra:xdebug-php-81}` |

 Note that the "Memcached" layer provides both extension for [Memcache](https://pecl.php.net/package/memcache) and [Memcached](https://pecl.php.net/package/memcached).
```

Once you have done above please submit the PR.
