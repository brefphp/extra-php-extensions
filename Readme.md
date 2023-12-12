# Bref Extra PHP Extension

This repository provides PHP extensions for [Bref](https://bref.sh/) applications via AWS Lambda layers.

This is useful when you want something "off the shelf".
If you ever need more than 2-3 layer you should consider creating your own layer. That is because AWS has
a limit of 5 layers per Lambda. You can also utilise the provided docker images for local development.

> **Note**
>
> If you are using Bref v2, you need to use version `1.x` of the `bref/extra-php-extensions` package.
>
> If you are using Bref v1, you need to use version `0.x` of the `bref/extra-php-extensions` package.


> **Warning**
> 
> **ARM64 is not supported yet with Bref v2.**


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

plugins:
    - ./vendor/bref/bref
    - ./vendor/bref/extra-php-extensions # <----- Add the extra Serverless plugin

functions:
    console:
        handler: bin/console
        runtime: php-81
        layers:
            - ${bref-extra:amqp-php-81} # <----- Example for AMQP layer
```

### Available layers

| Name             | Serverless config (php 8.1)            |
|:-----------------|:---------------------------------------|
| AMQP             | `${bref-extra:amqp-php-81}`            |
| Blackfire        | `${bref-extra:blackfire-php-81}`       |
| Bsdiff           | `${bref-extra:bsdiff-php-81}`          |
| Calendar         | `${bref-extra:calendar-php-81}`        |
| Cassandra        | `${bref-extra:cassandra-php-81}`       |
| Datadog          | `${bref-extra:datadog-php-81}`         |
| Decimal          | `${bref-extra:decimal-php-81}`         |
| DS               | `${bref-extra:ds-php-81}`              |
| Elastic APM      | `${bref-extra:elastic-apm-php-81}`     |
| Excimer          | `${bref-extra:excimer-php-81}`         |
| GD               | `${bref-extra:gd-php-81}`              |
| gnupg            | `${bref-extra:gnupg-php-81}`           |
| GMP              | `${bref-extra:gmp-php-81}`             |
| gRPC             | `${bref-extra:grpc-php-81}`            |
| Igbinary         | `${bref-extra:igbinary-php-81}`        |
| Imagick          | `${bref-extra:imagick-php-81}`         |
| IMAP             | `${bref-extra:imap-php-81}`            |
| LDAP             | `${bref-extra:ldap-php-81}`            |
| Mailparse        | `${bref-extra:mailparse-php-81}`       |
| MaxMind DB       | `${bref-extra:maxminddb-php-81}`       |
| Memcache         | `${bref-extra:memcache-php-81}`        |
| Memcached        | `${bref-extra:memcached-php-81}`       |
| MongoDB          | `${bref-extra:mongodb-php-81}`         |
| MsgPack          | `${bref-extra:msgpack-php-81}`         |
| Newrelic         | `${bref-extra:newrelic-php-81}`        |
| ODBC Snowflake   | `${bref-extra:odbc-snowflake-php-81}`  |
| OpenSwoole       | `${bref-extra:openswoole-php-81}`      |
| OpenTelemetry    | `${bref-extra:opentelemetry-php-81}`   |
| Oracle           | `${bref-extra:oci8-php-80}`            |
| Pcov             | `${bref-extra:pcov-php-81}`            |
| PostgreSQL       | `${bref-extra:pgsql-php-81}`           |
| RdKafka          | `${bref-extra:rdkafka-php-81}`         |
| Redis (phpredis) | `${bref-extra:redis-php-81}`           |
| Redis-Igbinary   | `${bref-extra:redis-igbinary-php-81}`  |
| Relay            | `${bref-extra:relay-php-81}`           |
| Scout APM        | `${bref-extra:scoutapm-php-81}`        |
| Scrypt           | `${bref-extra:scrypt-php-81}`          |
| SNMP             | `${bref-extra:snmp-php-81}`            |
| SPX              | `${bref-extra:spx-php-81}`             |
| SSH2             | `${bref-extra:ssh2-php-81}`            |
| Swoole           | `${bref-extra:swoole-php-81}`          |
| Symfony Runtime  | `${bref-extra:symfony-runtime-php-81}` |
| Microsoft SQLSRV | `${bref-extra:sqlsrv-php-81}`          |
| Tideways         | `${bref-extra:tideways-php-81}`        |
| Tidy             | `${bref-extra:tidy-php-81}`            |
| UUID             | `${bref-extra:uuid-php-81}`            |
| Xdebug           | `${bref-extra:xdebug-php-81}`          |
| Xlswriter        | `${bref-extra:xlswriter-php-81}`       |
| xmlrpc           | `${bref-extra:xmlrpc-php-81}`          |
| Yaml             | `${bref-extra:yaml-php-81}`            |

### Blackfire installation

The Blackfire layer only have the probe installed.

You still need to install the agent.
The agent is installed on a separate server (not a lambda function). The micro
EC2 instance is sufficient to run the Blackfire agent.

Create a `blackfire.ini` file in `php/conf.d/` for your lambda function where you load the extension
and modify the `agent_socket` in order to point it to the Blackfire Agent.

```ini
;php/conf.d/blackfire.ini
blackfire.agent_socket = tcp://ip-172-40-40-40.eu-central-1.compute.internal:8307
blackfire.agent_timeout = 0.25
```

> You may tweak other Blackfire parameters.
> [See Blackfire documentation about them](https://blackfire.io/docs/configuration/php#configuring-the-probe-via-the-php-ini-configuration-file).

Then modify your [agent config](https://blackfire.io/docs/reference-guide/configuration#agent-configuration)
to make sure you are listening to `tcp://0.0.0.0:8307`.

This [blog post](https://developer.happyr.com/installing-blackfire-multiple-servers)
could be helpful as it describes how to install the Blackfire Agent.

### ODBC Snowflake setup

You need to set the environment variable `ODBCSYSINI: /opt/snowflake_odbc/conf/` in your `serverless.yaml`
in order to tell unixODBC to load the ini file of the snowflake client.

You can then use snowflake like this: `odbc_connect('DRIVER=SnowflakeDSIIDriver;Server=[name].snowflakecomputing.com;Account=;Schema=;Warehouse=;Database=;Role=', 'username', 'password')`.

There is more information about the driver ini configuration in the [snowflake client documentation](https://docs.snowflake.com/en/user-guide/odbc-linux.html#step-4-configure-the-odbc-driver)
but the default configuration is enough in most cases.
The easiest way review those is to download the [`snowflake_odbc` directory](https://sfc-repo.snowflakecomputing.com/odbc/linux/index.html).

### Symfony Runtime

Read [docs at runtime/bref](https://github.com/php-runtime/bref).

### SQL Server setup

The SQL Server layer includes both the SQLSRV extension and the PDO_SQLSRV extension ([source](https://github.com/microsoft/msphpsql)).
If you are unsure of which extension to use, this [stackoverflow post](https://stackoverflow.com/questions/11614536/sqlsrv-driver-vs-pdo-driver-for-php-with-ms-sql-server) may be helpful.
You need to set the environment variable `ODBCSYSINI: /opt/microsoft/conf/`
in your `serverless.yaml` in order to tell unixODBC to load the required ini files.

### New Relic

Read [the New Relic tutorial](docs/newrelic.md).

### Datadog

Read [the Datadog tutorial](docs/datadog.md).

## Docker images

There are Docker images for every layer. They are updated on every push to master
and on every tag. The name of the image is `bref/extra-[name]-php-[version]`. Find
all images on [Docker hub](https://hub.docker.com/u/bref).

These are the same docker images that creates the layers. All layer files lives inside
the `/opt` directory in the image.

### Local Development

When developing locally you can build your own images with the required extensions. Example with PHP 8.2 and MongoDB Extension:

docker-compose.yml
```
  php:
    build:
      context: .
      dockerfile: Dockerfile-phpFpm
    volumes:
      - .:/var/task:ro
```

Dockerfile-phpFpm
```
FROM bref/php-82-fpm-dev:2
COPY --from=bref/extra-mongodb-php-82:1 /opt /opt
```

## For contributors and maintainers

### Creating a new layer

The idea is to start from bref/build-php-XX, install all libraries and extensions
you want, then move all related files to `/opt`. Those files will be available in
the same location on the Lambda.

Note that one can't just move files/libraries around. Most of them are expected to
be in their "standard" location.

1. Create a new folder in `layers` and name it to your extension name.
1. Add your Dockerfile
1. Create a config.json file at root of your layer directory specifying php versions it is built for
1. Create a test.php file with a small test that makes sure the extension is loaded
1. Update the table in the readme

Please refer [here](docs/create_your_own_extension_layer.md) for more details.

### Testing the layer

```
# Test all layers and PHP versions
make test

# Test only a single layer
layer=imagick make test

# Test a single layer on a single PHP version
layer=imagick php_versions=81 make test
```

You can publish the layer in your AWS account to test it in AWS Lambda as well:

```
# Publish a single layer on a single PHP version in a single region
layer=imagick php_versions=81 only_region=us-east-1 make publish
```

### Deploy new versions

#### Use Github actions

Prepare the changelog with some release notes. Then push your changes to `prepare-release` branch.
The Github Action will build an publish layers and then commit the `layers.json` to your PR.

Now you will just merge and create a tag.

#### The manual way

```
export AWS_PROFILE=my_profile
make publish
git add layers.json
git commit -m "New version of layers"
git push
```

#### Config

You can also build only one specific layer by providing `layer=blackfire` to `make`.
Same thing for some specific version(s) of php by providing `php_versions="80 81"` to `make`.
You can invoke both ways:

```shell
# First way: make with named arguments
make layer=gd php_versions=81
# Second way: environment variables passed to make
layer=blackfire php_versions=81 make
```

## Lambda layers in details

> **Notice:** this section is only useful if you want to learn more.

The lambda layers follow this pattern:

```
arn:aws:lambda:<region>:403367587399:layer:<layer-name>:<layer-version>
```

See the [latest layer versions](https://raw.githubusercontent.com/brefphp/extra-php-extensions/master/layers.json).
