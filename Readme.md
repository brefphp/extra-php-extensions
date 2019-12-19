# Bref layers

Create small layers with specific PHP extensions. This is useful when you want something "off the shelf". 
If you ever need more than one layer you should consider creating your own layer. That is because AWS has
a limit of 5 layers per Lambda. 

```yaml
# serverless.yml
service: app

provider:
    name: aws
    region: us-east-1
    runtime: provided

plugins:
    - ./vendor/bref/bref
    - ./vendor/bref/extra-layers

functions:
    console:
        handler: bin/console
        layers:
            - ${bref:layer.php-73} 
            - ${bref:extra.amqp} # AMQP layer
            - ${bref:layer.console}
```

```
;php/conf.d/php.ini
extension=amqp
```
