# Bref Extra PHP Extension

This repository has some AWS Lambda layers with PHP extensions. This is useful when you want something "off the shelf". 
If you ever need more than 2-3 layer you should consider creating your own layer. That is because AWS has
a limit of 5 layers per Lambda.

We are happy to get contributions for other extensions. Sky is the limit! (And also your knowledge with Docker...)

```yaml
# serverless.yml
service: app

provider:
    name: aws
    region: us-east-1
    runtime: provided

plugins:
    - ./vendor/bref/bref
    - ./vendor/bref/extra-layers # <--- Add the extra Serverless plugin

functions:
    console:
        handler: bin/console
        layers:
            - ${bref:layer.php-74} 
            - ${bref:extra.amqp-php-74} # <----- Example for AMQP layer
            - ${bref:layer.console}
```

```
;php/conf.d/php.ini
extension=/opt/bref-extra/amqp.so
```

## Available layers

| Name | Serverless config (php 7.4) | php.ini config |
| ---- | ----------------------------| -------------- |
| AMQP | `${bref:extra.amqp-php-74}` | `extension=/opt/bref-extra/amqp.so` |
| Blackfire | `${bref:extra.blackfire-php-74}` | `extension=/opt/bref-extra/blackfire.so` |
| Xdebug | `${bref:extra.xdebug-php-74}` | `zend_extension=/opt/bref-extra/xdebug.so` |

## Deploy new versions

```
make publish
make list
git add checksums.json layers.json
git commit -m "New version of layers"
git push
```
