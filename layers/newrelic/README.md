# Bref New Relic Extension

This is a Dockerfile to help add New Relic's PHP Agent to the Bref runtime so monitoring your applications on Lambda is possible. 

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
            - ${bref:extra.newrelic-php-74}
            - ${bref:layer.console}
```

### Extra configuration files

The configuration is only set to call newrelic.so to load the PHP Agent. In order to make this work you must configure the agent settings in your application and pass a name for the monitored application, a license key for your account and the IP/address of the New Relic PHP daemon listening in ECS or on a machine. 

You can if you wish set these settings as Environment Variables and set the values in the Lambda Environment Variables to control these settings at runtime. If you use Environment variables it's recommended to use some key management service like KMS.

You should configure your application in `php/conf.d/newrelic-settings.ini` within your Bref Application. This will mean it's controlled by your application code. 

```yaml
    NEW_RELIC_APP_NAME: "BrefApp"
    NEW_RELIC_LICENSE_KEY: "ceaxxxxxxxxxxxxxxxxxxxxceafe3"
    NEW_RELIC_DAEMON_ADDRESS: "xx.xx.xxx.xxx:31339"
    NEW_RELIC_LOG_FILE: "/proc/self/fd/2"
    NEW_RELIC_LOG_LEVEL: "info"
    NEW_RELIC_DAEMON_APP_CONNECT_TIMEOUT: "5s"
    NEW_RELIC_DAEMON_START_TIMEOUT: "5s"You may add extra 
```   

We would recommend pointing the logfile to `/proc/self/fd/2` which is STDERR and you will therefore get logging to Cloudwatch in the event of an issue. Log Level defaults to INFO but if you only want issues and not conversational logging you can raise the level to `warning` or `error`. Daemon address should be the IP of the location of your remotely started daemon (see below for guidance.)

If you wish you use ENV VARS to achieve this. You can set the values to 

```yaml
newrelic.appname = ${NEW_RELIC_APP_NAME}
newrelic.license = ${NEW_RELIC_LICENSE_KEY}
newrelic.daemon.address = ${NEW_RELIC_DAEMON_ADDRESS}
newrelic.logfile = ${NEW_RELIC_LOG_FILE}
newrelic.loglevel = ${NEW_RELIC_LOG_LEVEL}
newrelic.daemon.app_connect_timeout = ${NEW_RELIC_DAEMON_APP_CONNECT_TIMEOUT}
newrelic.daemon.start_timeout = ${NEW_RELIC_DAEMON_START_TIMEOUT}
```

In this example, the New Relic Daemon will be running on ECS or an EC2 instance listening on port 31339. If you follow the New Relic documentation and just install the daemon to a host, you can have it listen for incoming connectivity.

If you wish to run the daemon as a container. This repo should be particularly useful.
https://github.com/newrelic/newrelic-php-daemon-docker

If you wish to install the daemon to a machine which Lambda can reach. You should follow the installation docs to enable the relevant repository.
https://docs.newrelic.com/docs/agents/php-agent/advanced-installation/using-newrelic-install-script

Then use install_daemon mode to only install the daemon to this machine.

```cli
sudo newrelic-install install_daemon
```

Once installed you can start it and tell it the port to listen on. Ensuring the agent will be able to reach it. 

```cli
/usr/bin/newrelic-daemon --address=0.0.0.0:31339
```

You may need to configure your Lambda to be in a VPC with public/private subnets allowing access to an Internet Gateway to reach the machine or potentially house the machines in the same VPC for local network communication.