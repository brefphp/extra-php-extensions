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

# Build the final image from the lambci image that is close to the production environment
FROM lambci/lambda:provided

#
# Add commands to copy files that required for final image.
#
```

The environment variable `PHP_VERSION` is passed from the Makefile as an argument 
to docker build. It may have values like: `72`, `73`, `74`. A docker image is created 
for each PHP_VERSION. If the build procedure of your extension differs for each version, 
you may use this variable to switch processing in Dockerfile.

There are some other env variables available,`PHP_BUILD_DIR` is `/tmp/build/php`, `INSTALL_DIR` is `/opt/bref`.

### Building your extension

Then you need to add two parts of Dockerfile, the following snippets are examples 
of build and copy parts respectively.

```Dockerfile

WORKDIR ${PHP_BUILD_DIR}/ext/pgsql
RUN phpize
RUN ./configure --with-pgsql=${INSTALL_DIR}
RUN make -j `nproc` && make install

RUN cp `php-config --extension-dir`/pgsql.so /tmp/pgsql.so
```

You may need to: 
 - download the source code, 
 - install the libraries required for compilation, 
 - perform pecl install, 
 - etc.

The Dockerfiles for [these](../layers) extensions could be very helpful.

### Copy files


The extension layer consists of a zip archive of files that overlay the PHP layer, 
so copy the files and create the layer file structure here. Extension and all related 
files that need to be installed should be placed `/opt` directory in the final image.
Because only `/opt` directory is allowed to put things in Lambda custom runtime
environment.

```Dockerfile
COPY --from=ext /tmp/pgsql.so /opt/bref-extra/pgsql.so
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

Contributions to add new extensions are welcomed. When you built new extension, please contribute it by all means.
In order to contribute, you should do a little more work.

* Make sure that it works with each PHP versions in the way described above
* Update the document and CI settings as follows

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
