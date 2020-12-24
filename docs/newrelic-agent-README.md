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

## Extra configuration files

Once you enable the New Relic Agent Layer the newrelic.so file containing the New Relic PHP Agent will be loaded. However this loads the agent without any known configuration. So we need to provide the agent with some basic information.

There are 2 requires entries. License Key and Daemon Address.
The License Key should be your New Relic account License Key to ensure this app reports to your account.
The daemon address should be the location of a remote New Relic PHP Daemon listening for an agent connection. This can be ran in a layer if you desired but can lead to race conditions in cold starts. So a ready listening daemon running on a container or available machine is advisable.

You should configure your settings for the New Relic PHP Agent in `php/conf.d/newrelic-settings.ini` within your Bref Application. This will mean your configuration of your agent is controlled in your deployed application code.

Below are some suggested configuration settings to use. 


```yaml
    NEW_RELIC_APP_NAME: "BrefApp"
    NEW_RELIC_LICENSE_KEY: "ceaxxxxxxxxxxxxxxxxxxxxceafe3"
    NEW_RELIC_DAEMON_ADDRESS: "xx.xx.xxx.xxx:31339"
    NEW_RELIC_LOG_FILE: "/proc/self/fd/2"
    NEW_RELIC_LOG_LEVEL: "info"
    NEW_RELIC_DAEMON_APP_CONNECT_TIMEOUT: "5s"
    NEW_RELIC_DAEMON_START_TIMEOUT: "5s"
```   

1) APP_NAME - Will define what your application will be named when reporting to NR. Failure to set this will result in default "PHP Application" being used as name.
2) LOG_FILE - We would recommend pointing the logfile to `/proc/self/fd/2` which is STDERR and you will therefore get logging to Cloudwatch in the event of an issue. 
3) LOG_LEVEL - defaults to INFO but if you only want issues and not conversational logging you can raise the level to `warning` or `error`. 
4) DAEMON_ADDRESS - Should be the IP of the location of your remotely started daemon (see below for guidance.) along with port it's listening on.


If you use a Key Management tool or want to define the values as environment variables in Lambda (through UI or a serverless.yml) You can use ENV VARS in your configuration file which could be achieved as an example below.

```yaml
newrelic.appname = ${NEW_RELIC_APP_NAME}
newrelic.license = ${NEW_RELIC_LICENSE_KEY}
newrelic.daemon.address = ${NEW_RELIC_DAEMON_ADDRESS}
newrelic.logfile = ${NEW_RELIC_LOG_FILE}
newrelic.loglevel = ${NEW_RELIC_LOG_LEVEL}
newrelic.daemon.app_connect_timeout = ${NEW_RELIC_DAEMON_APP_CONNECT_TIMEOUT}
newrelic.daemon.start_timeout = ${NEW_RELIC_DAEMON_START_TIMEOUT}
```

## A Standalone Daemon
In this example, the New Relic Daemon will be running on ECS or an EC2 instance listening on port 31339. If you follow the New Relic documentation and just install the daemon to a host, you can have it listen for incoming connectivity.

You should follow the installation docs to enable the relevant repository.
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

If you wish to run the daemon as a container. This repo should be particularly useful.
https://github.com/newrelic/newrelic-php-daemon-docker

The same logic applies that the container must be reachable by the Lambda so configuring your security settings to allow the Lambda to reach the daemon will be important. If you use STDERR as suggested above, the Cloudwatch logs will be a useful guide if you are not having success connecting.