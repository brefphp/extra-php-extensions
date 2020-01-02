# Bref Extra PHP Extension

This repository has some AWS Lambda layers with PHP extensions. This is useful when you want something "off the shelf". 
If you ever need more than 2-3 layer you should consider creating your own layer. That is because AWS has
a limit of 5 layers per Lambda.

We are happy to get contributions for other extensions. Sky is the limit! (And also your knowledge with Docker...)

## Install and configure

```cli
composer require bref/extra-php-extensions
```

```yaml
# serverless.yml
service: app

provider:
    name: aws
    region: us-east-1
    runtime: provided

plugins:
    - ./vendor/bref/bref
    - ./vendor/bref/extra-php-extensions # <----- Add the extra Serverless plugin

functions:
    console:
        handler: bin/console
        layers:
            - ${bref:layer.php-74} 
            - ${bref:extra.amqp-php-74} # <----- Example for AMQP layer
            - ${bref:layer.console}
```

```ini
;php/conf.d/php.ini
extension=/opt/bref-extra/amqp.so
```

### Available layers

| Name | Serverless config (php 7.4) | php.ini config |
| ---- | ----------------------------| -------------- |
| AMQP | `${bref:extra.amqp-php-74}` | `extension=/opt/bref-extra/amqp.so` |
| Blackfire | `${bref:extra.blackfire-php-74}` | `extension=/opt/bref-extra/blackfire.so` |
| GMP | `${bref:extra.gmp-php-74}` | `extension=/opt/bref-extra/gmp.so` |
| Memcache | `${bref:extra.memcached-php-74}` | `extension=/opt/bref-extra/memcache.so` |
| Memcached | `${bref:extra.memcached-php-74}` | `extension=/opt/bref-extra/memcached.so` |
| Xdebug | `${bref:extra.xdebug-php-74}` | `zend_extension=/opt/bref-extra/xdebug.so` |

Note that the "Memcached" layer provides both extension for [Memcache](https://pecl.php.net/package/memcache) and [Memcached](https://pecl.php.net/package/memcached). 

### Blackfire installation

The Blackfire layer only have probe installed. You still need to install the agent. 
The agent is installed on a separate server (not a lambda function). The smallest 
EC2 instance is sufficient to run the Blackfire agent.

Create a `blackfire.ini` file for your lambda function where you load the extension 
and modify the `agent_socket`.

```ini
extension=/opt/bref-extra/blackfire.so

blackfire.agent_socket = tcp://ip-172-40-40-40.eu-central-1.compute.internal:8307
blackfire.agent_timeout = 0.25
```

Then modify your [agent config](https://blackfire.io/docs/reference-guide/configuration#agent-configuration) 
to make sure you are listening to `tcp://0.0.0.0:8307`.  

## For contributors and maintainers

### Creating a new layer

The idea is to start from bref/build-php-XX, install all libraries and extensions
you want, then move all related files to `/opt`. Those files will be available in
the same same location on the Lambda.

Note that one can't just move files/libraries around. Most of them are expected to
be in their "standard" location.  

1. Create a new folder in `layers` and name it to your extension name.
2. Add your Dockerfile
3. Update .travis.yml to include your extension
4. Update the table in the readme

### Deploy new versions

```
make publish
git add checksums.json layers.json
git commit -m "New version of layers"
git push
```
